export default class View extends $e.components.get( 'nested-elements/nested-repeater' ).exports.NestedViewBase {	
  constructor() {
    super(...arguments);
    this.isRendering = false;
    this.itemTitle = 'item_title';
    this.itemLink = 'item_link';
    this.internalUrl = 'internal-url';
    this.itemLinkSelector = '.elementor-control-item_link';
  }
  filter(child, index) {
    child.attributes.dataIndex = index + 1;
    child.attributes.widgetId = child.id;
    return true;
  }
  onAddChild(childView) {
    const widgetNumber = childView._parent.$el.find('.e-n-menu')[0]?.dataset.widgetNumber || childView.model.attributes.widgetId,
      index = childView.model.attributes.dataIndex,
      tabId = childView._parent.$el.find(`.e-n-menu-item-title[data-tab-index="${index}"]`)?.attr('id') || childView.model.attributes.widgetId + ' ' + index;
    childView.$el.attr({
      id: 'e-n-menu-content-' + widgetNumber + '' + index,
      role: 'menu',
      'aria-labelledby': tabId,
      'data-tab-index': index
    });
  }
  getChildViewContainer(containerView, childView) {
    const {
      elements_placeholder_selector: customSelector,
      child_container_placeholder_selector: childContainerSelector
    } = this.model.config.defaults;
    if (childView !== undefined && childView._index !== undefined && childContainerSelector) {
      return containerView.$el.find(`${childContainerSelector}`)[childView._index];
    }
    if (customSelector) {
      return containerView.$el.find(this.model.config.defaults.elements_placeholder_selector);
    }
    return super.getChildViewContainer(containerView, childView);
  }
  attachBuffer(compositeView, buffer) {
    const $container = this.getChildViewContainer(compositeView);
    if (this.model?.config?.support_improved_repeaters && this.model?.config?.is_interlaced) {
      const childContainerSelector = this.model?.config?.defaults?.child_container_placeholder_selector || '',
        childContainerClass = childContainerSelector.replace('.', '');
      this._updateChildContainers($container[0], childContainerClass, buffer);
    } else {
      $container.append(buffer);
    }
  }
  _updateChildContainers(wrapper, childContainerClass, buffer) {
    let index = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 0;
    _.each(wrapper.children, childContainer => {
      if (childContainer.classList?.contains(childContainerClass)) {
        const numberOfItems = buffer.childNodes.length;
        childContainer.appendChild(buffer.childNodes[0]);
        buffer.appendChild(childContainer);
        wrapper.append(buffer.childNodes[numberOfItems - 1]);
        index++;
      } else {
        this._updateChildContainers(childContainer, childContainerClass, buffer, index);
      }
    });
  }
  /**
   * Function renderOnChange().
   *
   * Render the changes in the settings according to the current situation.
   *
   * @param {Object} settings
   * @param {Array}  widget
   */
  renderOnChange(settings) {
    let widget = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
    if (!this.allowRender) {
      return;
    }

    // TODO: delete in 3.27.0
    if (this.isRendering) {
      this.isRendering = false;
      return;
    }
    const renderResult = this.renderDataBindings(settings, this.dataBindings, widget);
    if (renderResult instanceof Promise) {
      renderResult.then(result => {
        if (!result) {
          this.renderChanges(settings);
        }
      });
    }
    if (!renderResult) {
      this.renderChanges(settings);
    }
  }

  /**
   * Function renderDataBindings().
   *
   * Render linked data.
   *
   * @param {Object} settings
   * @param {Array}  dataBindings
   * @param {Array}  widget
   *
   * @return {boolean} - false on fail.
   */
  renderDataBindings(settings, dataBindings) {
    let widget = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
    if (!this.dataBindings?.length) {
      return false;
    }
    let changed = false;
    const renderDataBinding = async dataBinding => {
      if (undefined !== settings.changed[dataBinding.dataset.bindingSetting]) {
        dataBinding.el.innerHTML = settings.changed[dataBinding.dataset.bindingSetting];
        return true;
      }
      if (!settings?.changed.__dynamic__ || !widget.length) {
        return false;
      }
      if (!this.isTitleOrLinkChanged(settings)) {
        return true;
      }
      const {
          bindingSetting
        } = dataBinding.dataset,
        changedControl = this.getChangedDynamicControlKey(settings);
      let change = settings.changed[bindingSetting];
      if (this.isInternalUrl(settings?.changed?.__dynamic__?.item_link) && this.isSettingChanged(settings, this.itemLink)) {
        return await this.getDynamicValue(settings, changedControl, bindingSetting, dataBinding, widget);
      }
      if (this.isAtomicDynamic(settings.changed, dataBinding, changedControl)) {
        const dynamicValue = await this.getDynamicValue(settings, changedControl, bindingSetting, dataBinding, widget);
        if (this.itemLink === changedControl) {
          return true;
        }
        if (dynamicValue) {
          change = dynamicValue;
        }
      }
      if (change !== undefined) {
        dataBinding.el.innerHTML = change;
        return true;
      }
      return false;
    };
    for (const dataBinding of dataBindings) {
      switch (dataBinding.dataset.bindingType) {
        case 'repeater-item':
          {
            const repeater = this.container.repeaters[dataBinding.dataset.bindingRepeaterName];
            if (!repeater) {
              break;
            }
            const container = repeater.children.find(i => i.id === settings.attributes._id);
            if (container?.parent?.children.indexOf(container) + 1 === parseInt(dataBinding.dataset.bindingIndex)) {
              changed = renderDataBinding(dataBinding);
            } else if (dataBindings.indexOf(dataBinding) + 1 === this.getRepeaterItemActiveIndex()) {
              if (this.isItemLinkChild(widget)) {
                return true;
              }
              changed = this.tryHandleDynamicCoverSettings(dataBinding, settings);
            }
          }
          break;
        case 'content':
          {
            changed = renderDataBinding(dataBinding);
          }
          break;
      }
      if (changed) {
        break;
      }
    }
    return changed;
  }
  isAtomicDynamic(changedSettings, dataBinding, changedControl) {
    return dataBinding.el.hasAttribute('data-binding-dynamic') && (this.itemTitle === changedControl || this.itemLink === changedControl);
  }
  async getDynamicValue(settings, changedControlKey, bindingSetting, dataBinding, widget) {
    const dynamicSettings = {
        active: true
      },
      valueToParse = this.extractValueToParse(this.getChangedData(settings, changedControlKey, bindingSetting));
    if (undefined === valueToParse) {
      return settings.attributes[changedControlKey];
    }
    const data = await this.getDataFromCacheOrBackend(valueToParse, dynamicSettings);
    if (this.itemTitle === changedControlKey) {
      return data;
    }
    if (undefined !== data) {
      this.tryFormatDynamicMegaMenuUrl(valueToParse, dataBinding, widget, changedControlKey, dynamicSettings);
    }
    return settings.attributes[changedControlKey];
  }
  extractValueToParse(valueToParse) {
    let keyToExtract = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'url';
    if ('object' === typeof valueToParse) {
      return valueToParse[keyToExtract];
    }
    return valueToParse;
  }

  // TODO: delete in 3.27.0
  getChangedDynamicControlKey(settings) {
    if (!settings?.changed?.__dynamic__) {
      return Object.keys(settings.changed)[0];
    }
    const changedControlKey = this.findUniqueKey(settings?.changed?.__dynamic__, settings?._previousAttributes?.__dynamic__)[0];
    if (changedControlKey) {
      return changedControlKey;
    }
    return this.isSettingChanged(settings, this.itemLink) ? this.itemLink : this.itemTitle;
  }
  tryFormatDynamicMegaMenuUrl(valueToParse, dataBinding, widget, changedControl, dynamicSettings) {
    const dynamicTagName = this.getDynamicTagName(valueToParse);
    if (this.itemLink !== changedControl || 'internal_link' === dynamicTagName) {
      return false;
    }
    const value = elementor.dynamicTags.parseTagsText(valueToParse, dynamicSettings, elementor.dynamicTags.getTagDataContent);
    elementor.$preview[0].contentWindow.dispatchEvent(new CustomEvent('elementor/dynamic/url_change', {
      detail: {
        element: dataBinding.el,
        actionName: valueToParse && dynamicTagName,
        value
      }
    }));
    dataBinding.el = Array.from(widget)[0].querySelectorAll('.e-n-menu-title-text')[dataBinding.dataset.bindingIndex - 1];
  }
  getDynamicTagName(changedDataForAddedItem) {
    const regex = /name="([^"]*)"/;
    const match = changedDataForAddedItem.match(regex);
    return match ? match[1] : null;
  }
  isInternalUrl(dynamicData) {
    if (!dynamicData) {
      return false;
    }
    return this.internalUrl === this.getDynamicTagName(dynamicData);
  }
  isItemLinkChild(widget) {
    return widget[0].closest(this.itemLinkSelector);
  }

  // TODO: delete in 3.27.0
  async getDataFromCacheOrBackend(valueToParse, dynamicSettings) {
    try {
      return elementor.dynamicTags.parseTagsText(valueToParse, dynamicSettings, elementor.dynamicTags.getTagDataContent);
    } catch {
      await new Promise(resolve => {
        elementor.dynamicTags.refreshCacheFromServer(() => {
          resolve();
        });
      });
      return !_.isEmpty(elementor.dynamicTags.cache) ? elementor.dynamicTags.parseTagsText(valueToParse, dynamicSettings, elementor.dynamicTags.getTagDataContent) : false;
    }
  }

  // TODO: delete in 3.27.0
  getChangedDataForRemovedItem(settings, changedControlKey, bindingSetting) {
    return settings.attributes?.[changedControlKey]?.[bindingSetting] || settings.attributes?.[changedControlKey];
  }

  // TODO: delete in 3.27.0
  getChangedDataForAddedItem(settings, changedControlKey, bindingSetting) {
    return settings.attributes?.__dynamic__?.[changedControlKey]?.[bindingSetting] || settings.attributes?.__dynamic__?.[changedControlKey];
  }

  // TODO: delete in 3.27.0
  getChangedData(settings, changedControlKey, bindingSetting) {
    const changedDataForRemovedItem = this.getChangedDataForRemovedItem(settings, changedControlKey, bindingSetting),
      changedDataForAddedItem = this.getChangedDataForAddedItem(settings, changedControlKey, bindingSetting);
    return changedDataForAddedItem || changedDataForRemovedItem;
  }

  /**
   * Function getTitleWithAdvancedValues().
   *
   * Renders before / after / fallback for dynamic item titles.
   *
   * @param {Object} settings
   * @param {string} text
   */
  // TODO: delete in 3.27.0
  getTitleWithAdvancedValues(settings, text) {
    const {
      attributes,
      _previousAttributes: previousAttributes
    } = settings;
    if (this.compareSettings(attributes, previousAttributes, 'fallback')) {
      text = text.replace(new RegExp(previousAttributes.fallback), '');
    }
    if (!text || attributes.fallback === text) {
      return attributes.fallback || '';
    }
    if (this.compareSettings(attributes, previousAttributes, 'before')) {
      text = text.replace(previousAttributes.before, '');
    }
    if (this.compareSettings(attributes, previousAttributes, 'after')) {
      text = text.replace(new RegExp(previousAttributes.after + '$'), '');
    }
    if (!text) {
      return attributes.fallback || '';
    }
    const newBefore = this.getNewSettingsValue(attributes, previousAttributes, 'before'),
      newAfter = this.getNewSettingsValue(attributes, previousAttributes, 'after');
    text = newBefore + text;
    text += newAfter;
    return text;
  }

  // TODO: delete in 3.27.0
  compareSettings(attributes, previousAttributes, key) {
    return previousAttributes[key] && previousAttributes[key] !== attributes[key];
  }

  // TODO: delete in 3.27.0
  getNewSettingsValue(attributes, previousAttributes, key) {
    return previousAttributes[key] !== attributes[key] ? attributes[key] || '' : '';
  }

  // TODO: delete in 3.27.0
  getRepeaterItemActiveIndex() {
    return this.getContainer().renderer.view.model.changed.editSettings.changed.activeItemIndex || this.getContainer().renderer.view.model.changed.editSettings.attributes.activeItemIndex;
  }

  // TODO: delete in 3.27.0
  tryHandleDynamicCoverSettings(dataBinding, settings) {
    if (!this.isAdvancedDynamicSettings(settings.attributes)) {
      return false;
    }
    this.isRendering = true;
    dataBinding.el.textContent = this.getTitleWithAdvancedValues(settings, dataBinding.el.textContent);
    return true;
  }

  // TODO: delete in 3.27.0
  isAdvancedDynamicSettings(attributes) {
    return 'before' in attributes && 'after' in attributes && 'fallback' in attributes;
  }
  isSettingChanged(settings, bindingSettings) {
    return settings.attributes.__dynamic__?.[bindingSettings] !== settings._previousAttributes.__dynamic__?.[bindingSettings];
  }
  isTitleOrLinkChanged(settings) {
    return this.isSettingChanged(settings, this.itemTitle) || this.isSettingChanged(settings, this.itemLink);
  }
}
