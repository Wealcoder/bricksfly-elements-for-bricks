import View from './views/view';
export class AAENestedMegaMenu extends elementor.modules.elements.types.NestedElementBase {
	getType() {
		return 'aae-nested-mega-menu';
	}

	getView() {		  
		return View;
	}

	getInitialConfig() {
		return {
			...super.getInitialConfig(),
			defaults: {
				repeater_title_setting: 'item_title',
			},
		};
	}
}

// Initialize when nested elements are loaded
elementorCommon.elements.$window.on( 'elementor/nested-element-type-loaded', () => {
	// Register the nested mega menu element type
	elementor.elementsManager.registerElementType( new AAENestedMegaMenu() );
} );

export default AAENestedMegaMenu;


