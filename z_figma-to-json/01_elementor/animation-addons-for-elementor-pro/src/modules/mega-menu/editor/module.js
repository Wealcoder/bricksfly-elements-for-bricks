import AAENestedMegaMenu from './nested-mega-menu';

export default class Module {
	constructor() {
		// Wait for document ready and Elementor to be fully loaded
		jQuery(document).ready(() => {
			// Wait for Elementor to be fully initialized
			const initElement = () => {
				if (typeof elementor !== 'undefined' && elementor.elementsManager) {
					elementor.elementsManager.registerElementType( new AAENestedMegaMenu() );
				} else {
					// Retry after a short delay if Elementor isn't ready yet
					setTimeout(initElement, 100);
				}
			};
			
			initElement();
		});
	}
}
