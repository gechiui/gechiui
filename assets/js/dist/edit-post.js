/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 4403:
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;
	var nativeCodeString = '[native code]';

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
					classes.push(arg.toString());
					continue;
				}

				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "PluginBlockSettingsMenuItem": function() { return /* reexport */ plugin_block_settings_menu_item; },
  "PluginDocumentSettingPanel": function() { return /* reexport */ plugin_document_setting_panel; },
  "PluginMoreMenuItem": function() { return /* reexport */ plugin_more_menu_item; },
  "PluginPostPublishPanel": function() { return /* reexport */ plugin_post_publish_panel; },
  "PluginPostStatusInfo": function() { return /* reexport */ plugin_post_status_info; },
  "PluginPrePublishPanel": function() { return /* reexport */ plugin_pre_publish_panel; },
  "PluginSidebar": function() { return /* reexport */ PluginSidebarEditPost; },
  "PluginSidebarMoreMenuItem": function() { return /* reexport */ PluginSidebarMoreMenuItem; },
  "__experimentalFullscreenModeClose": function() { return /* reexport */ fullscreen_mode_close; },
  "__experimentalMainDashboardButton": function() { return /* reexport */ main_dashboard_button; },
  "initializeEditor": function() { return /* binding */ initializeEditor; },
  "reinitializeEditor": function() { return /* binding */ reinitializeEditor; },
  "store": function() { return /* reexport */ store_store; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/interface/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "closeModal": function() { return closeModal; },
  "disableComplementaryArea": function() { return disableComplementaryArea; },
  "enableComplementaryArea": function() { return enableComplementaryArea; },
  "openModal": function() { return openModal; },
  "pinItem": function() { return pinItem; },
  "setDefaultComplementaryArea": function() { return setDefaultComplementaryArea; },
  "setFeatureDefaults": function() { return setFeatureDefaults; },
  "setFeatureValue": function() { return setFeatureValue; },
  "toggleFeature": function() { return toggleFeature; },
  "unpinItem": function() { return unpinItem; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/interface/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getActiveComplementaryArea": function() { return getActiveComplementaryArea; },
  "isComplementaryAreaLoading": function() { return isComplementaryAreaLoading; },
  "isFeatureActive": function() { return isFeatureActive; },
  "isItemPinned": function() { return isItemPinned; },
  "isModalActive": function() { return isModalActive; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/edit-post/build-module/store/actions.js
var store_actions_namespaceObject = {};
__webpack_require__.r(store_actions_namespaceObject);
__webpack_require__.d(store_actions_namespaceObject, {
  "__experimentalSetPreviewDeviceType": function() { return __experimentalSetPreviewDeviceType; },
  "__unstableCreateTemplate": function() { return __unstableCreateTemplate; },
  "__unstableSwitchToTemplateMode": function() { return __unstableSwitchToTemplateMode; },
  "closeGeneralSidebar": function() { return closeGeneralSidebar; },
  "closeModal": function() { return actions_closeModal; },
  "closePublishSidebar": function() { return closePublishSidebar; },
  "hideBlockTypes": function() { return hideBlockTypes; },
  "initializeMetaBoxes": function() { return initializeMetaBoxes; },
  "metaBoxUpdatesFailure": function() { return metaBoxUpdatesFailure; },
  "metaBoxUpdatesSuccess": function() { return metaBoxUpdatesSuccess; },
  "openGeneralSidebar": function() { return openGeneralSidebar; },
  "openModal": function() { return actions_openModal; },
  "openPublishSidebar": function() { return openPublishSidebar; },
  "removeEditorPanel": function() { return removeEditorPanel; },
  "requestMetaBoxUpdates": function() { return requestMetaBoxUpdates; },
  "setAvailableMetaBoxesPerLocation": function() { return setAvailableMetaBoxesPerLocation; },
  "setIsEditingTemplate": function() { return setIsEditingTemplate; },
  "setIsInserterOpened": function() { return setIsInserterOpened; },
  "setIsListViewOpened": function() { return setIsListViewOpened; },
  "showBlockTypes": function() { return showBlockTypes; },
  "switchEditorMode": function() { return switchEditorMode; },
  "toggleEditorPanelEnabled": function() { return toggleEditorPanelEnabled; },
  "toggleEditorPanelOpened": function() { return toggleEditorPanelOpened; },
  "toggleFeature": function() { return actions_toggleFeature; },
  "togglePinnedPluginItem": function() { return togglePinnedPluginItem; },
  "togglePublishSidebar": function() { return togglePublishSidebar; },
  "updatePreferredStyleVariations": function() { return updatePreferredStyleVariations; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/edit-post/build-module/store/selectors.js
var store_selectors_namespaceObject = {};
__webpack_require__.r(store_selectors_namespaceObject);
__webpack_require__.d(store_selectors_namespaceObject, {
  "__experimentalGetInsertionPoint": function() { return __experimentalGetInsertionPoint; },
  "__experimentalGetPreviewDeviceType": function() { return __experimentalGetPreviewDeviceType; },
  "areMetaBoxesInitialized": function() { return areMetaBoxesInitialized; },
  "getActiveGeneralSidebarName": function() { return getActiveGeneralSidebarName; },
  "getActiveMetaBoxLocations": function() { return getActiveMetaBoxLocations; },
  "getAllMetaBoxes": function() { return getAllMetaBoxes; },
  "getEditedPostTemplate": function() { return getEditedPostTemplate; },
  "getEditorMode": function() { return getEditorMode; },
  "getHiddenBlockTypes": function() { return getHiddenBlockTypes; },
  "getMetaBoxesPerLocation": function() { return getMetaBoxesPerLocation; },
  "getPreference": function() { return getPreference; },
  "getPreferences": function() { return getPreferences; },
  "hasMetaBoxes": function() { return hasMetaBoxes; },
  "isEditingTemplate": function() { return selectors_isEditingTemplate; },
  "isEditorPanelEnabled": function() { return isEditorPanelEnabled; },
  "isEditorPanelOpened": function() { return isEditorPanelOpened; },
  "isEditorPanelRemoved": function() { return isEditorPanelRemoved; },
  "isEditorSidebarOpened": function() { return isEditorSidebarOpened; },
  "isFeatureActive": function() { return selectors_isFeatureActive; },
  "isInserterOpened": function() { return isInserterOpened; },
  "isListViewOpened": function() { return isListViewOpened; },
  "isMetaBoxLocationActive": function() { return isMetaBoxLocationActive; },
  "isMetaBoxLocationVisible": function() { return isMetaBoxLocationVisible; },
  "isModalActive": function() { return selectors_isModalActive; },
  "isPluginItemPinned": function() { return isPluginItemPinned; },
  "isPluginSidebarOpened": function() { return isPluginSidebarOpened; },
  "isPublishSidebarOpened": function() { return isPublishSidebarOpened; },
  "isSavingMetaBoxes": function() { return selectors_isSavingMetaBoxes; }
});

;// CONCATENATED MODULE: external ["gc","element"]
var external_gc_element_namespaceObject = window["gc"]["element"];
;// CONCATENATED MODULE: external ["gc","blocks"]
var external_gc_blocks_namespaceObject = window["gc"]["blocks"];
;// CONCATENATED MODULE: external ["gc","blockLibrary"]
var external_gc_blockLibrary_namespaceObject = window["gc"]["blockLibrary"];
;// CONCATENATED MODULE: external ["gc","deprecated"]
var external_gc_deprecated_namespaceObject = window["gc"]["deprecated"];
var external_gc_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_gc_deprecated_namespaceObject);
;// CONCATENATED MODULE: external ["gc","data"]
var external_gc_data_namespaceObject = window["gc"]["data"];
;// CONCATENATED MODULE: external ["gc","hooks"]
var external_gc_hooks_namespaceObject = window["gc"]["hooks"];
;// CONCATENATED MODULE: external ["gc","preferences"]
var external_gc_preferences_namespaceObject = window["gc"]["preferences"];
;// CONCATENATED MODULE: external ["gc","widgets"]
var external_gc_widgets_namespaceObject = window["gc"]["widgets"];
;// CONCATENATED MODULE: external ["gc","mediaUtils"]
var external_gc_mediaUtils_namespaceObject = window["gc"]["mediaUtils"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/hooks/components/index.js
/**
 * GeChiUI dependencies
 */



const replaceMediaUpload = () => external_gc_mediaUtils_namespaceObject.MediaUpload;

(0,external_gc_hooks_namespaceObject.addFilter)('editor.MediaUpload', 'core/edit-post/replace-media-upload', replaceMediaUpload);

;// CONCATENATED MODULE: external ["gc","components"]
var external_gc_components_namespaceObject = window["gc"]["components"];
;// CONCATENATED MODULE: external ["gc","blockEditor"]
var external_gc_blockEditor_namespaceObject = window["gc"]["blockEditor"];
;// CONCATENATED MODULE: external ["gc","i18n"]
var external_gc_i18n_namespaceObject = window["gc"]["i18n"];
;// CONCATENATED MODULE: external ["gc","compose"]
var external_gc_compose_namespaceObject = window["gc"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/hooks/validate-multiple-use/index.js


/**
 * GeChiUI dependencies
 */







const enhance = (0,external_gc_compose_namespaceObject.compose)(
/**
 * For blocks whose block type doesn't support `multiple`, provides the
 * wrapped component with `originalBlockClientId` -- a reference to the
 * first block of the same type in the content -- if and only if that
 * "original" block is not the current one. Thus, an inexisting
 * `originalBlockClientId` prop signals that the block is valid.
 *
 * @param {GCComponent} WrappedBlockEdit A filtered BlockEdit instance.
 *
 * @return {GCComponent} Enhanced component with merged state data props.
 */
(0,external_gc_data_namespaceObject.withSelect)((select, block) => {
  const multiple = (0,external_gc_blocks_namespaceObject.hasBlockSupport)(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


  const blocks = select(external_gc_blockEditor_namespaceObject.store).getBlocks();
  const firstOfSameType = blocks.find(({
    name
  }) => block.name === name);
  const isInvalid = firstOfSameType && firstOfSameType.clientId !== block.clientId;
  return {
    originalBlockClientId: isInvalid && firstOfSameType.clientId
  };
}), (0,external_gc_data_namespaceObject.withDispatch)((dispatch, {
  originalBlockClientId
}) => ({
  selectFirst: () => dispatch(external_gc_blockEditor_namespaceObject.store).selectBlock(originalBlockClientId)
})));
const withMultipleValidation = (0,external_gc_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => {
  return enhance(({
    originalBlockClientId,
    selectFirst,
    ...props
  }) => {
    if (!originalBlockClientId) {
      return (0,external_gc_element_namespaceObject.createElement)(BlockEdit, { ...props
      });
    }

    const blockType = (0,external_gc_blocks_namespaceObject.getBlockType)(props.name);
    const outboundType = getOutboundType(props.name);
    return [(0,external_gc_element_namespaceObject.createElement)("div", {
      key: "invalid-preview",
      style: {
        minHeight: '60px'
      }
    }, (0,external_gc_element_namespaceObject.createElement)(BlockEdit, {
      key: "block-edit",
      ...props
    })), (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.Warning, {
      key: "multiple-use-warning",
      actions: [(0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
        key: "find-original",
        variant: "secondary",
        onClick: selectFirst
      }, (0,external_gc_i18n_namespaceObject.__)('查找原件')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
        key: "remove",
        variant: "secondary",
        onClick: () => props.onReplace([])
      }, (0,external_gc_i18n_namespaceObject.__)('移除')), outboundType && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
        key: "transform",
        variant: "secondary",
        onClick: () => props.onReplace((0,external_gc_blocks_namespaceObject.createBlock)(outboundType.name, props.attributes))
      }, (0,external_gc_i18n_namespaceObject.__)('转换至：'), " ", outboundType.title)]
    }, (0,external_gc_element_namespaceObject.createElement)("strong", null, blockType?.title, ": "), (0,external_gc_i18n_namespaceObject.__)('此区块只能被使用一次。'))];
  });
}, 'withMultipleValidation');
/**
 * Given a base block name, returns the default block type to which to offer
 * transforms.
 *
 * @param {string} blockName Base block name.
 *
 * @return {?Object} The chosen default block type.
 */

function getOutboundType(blockName) {
  // Grab the first outbound transform.
  const transform = (0,external_gc_blocks_namespaceObject.findTransform)((0,external_gc_blocks_namespaceObject.getBlockTransforms)('to', blockName), ({
    type,
    blocks
  }) => type === 'block' && blocks.length === 1 // What about when .length > 1?
  );

  if (!transform) {
    return null;
  }

  return (0,external_gc_blocks_namespaceObject.getBlockType)(transform.blocks[0]);
}

(0,external_gc_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/edit-post/validate-multiple-use/with-multiple-validation', withMultipleValidation);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/hooks/index.js
/**
 * Internal dependencies
 */



;// CONCATENATED MODULE: external ["gc","coreData"]
var external_gc_coreData_namespaceObject = window["gc"]["coreData"];
;// CONCATENATED MODULE: external ["gc","editor"]
var external_gc_editor_namespaceObject = window["gc"]["editor"];
;// CONCATENATED MODULE: external ["gc","primitives"]
var external_gc_primitives_namespaceObject = window["gc"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/external.js


/**
 * GeChiUI dependencies
 */

const external = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"
}));
/* harmony default export */ var library_external = (external);

;// CONCATENATED MODULE: external ["gc","plugins"]
var external_gc_plugins_namespaceObject = window["gc"]["plugins"];
;// CONCATENATED MODULE: external ["gc","url"]
var external_gc_url_namespaceObject = window["gc"]["url"];
;// CONCATENATED MODULE: external ["gc","notices"]
var external_gc_notices_namespaceObject = window["gc"]["notices"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/plugins/copy-content-menu-item/index.js


/**
 * GeChiUI dependencies
 */






function CopyContentMenuItem() {
  const {
    createNotice
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_notices_namespaceObject.store);
  const getText = (0,external_gc_data_namespaceObject.useSelect)(select => () => select(external_gc_editor_namespaceObject.store).getEditedPostAttribute('content'), []);

  function onSuccess() {
    createNotice('info', (0,external_gc_i18n_namespaceObject.__)('已复制所有内容。'), {
      isDismissible: true,
      type: 'snackbar'
    });
  }

  const ref = (0,external_gc_compose_namespaceObject.useCopyToClipboard)(getText, onSuccess);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    ref: ref
  }, (0,external_gc_i18n_namespaceObject.__)('复制全部区块'));
}

;// CONCATENATED MODULE: external ["gc","keycodes"]
var external_gc_keycodes_namespaceObject = window["gc"]["keycodes"];
// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(4403);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/check.js


/**
 * GeChiUI dependencies
 */

const check = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
}));
/* harmony default export */ var library_check = (check);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/star-filled.js


/**
 * GeChiUI dependencies
 */

const starFilled = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ var star_filled = (starFilled);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/star-empty.js


/**
 * GeChiUI dependencies
 */

const starEmpty = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ var star_empty = (starEmpty);

;// CONCATENATED MODULE: external ["gc","viewport"]
var external_gc_viewport_namespaceObject = window["gc"]["viewport"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/close-small.js


/**
 * GeChiUI dependencies
 */

const closeSmall = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ var close_small = (closeSmall);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/actions.js
/**
 * GeChiUI dependencies
 */


/**
 * Set a default complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 *
 * @return {Object} Action object.
 */

const setDefaultComplementaryArea = (scope, area) => ({
  type: 'SET_DEFAULT_COMPLEMENTARY_AREA',
  scope,
  area
});
/**
 * Enable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 */

const enableComplementaryArea = (scope, area) => ({
  registry,
  dispatch
}) => {
  // Return early if there's no area.
  if (!area) {
    return;
  }

  const isComplementaryAreaVisible = registry.select(external_gc_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (!isComplementaryAreaVisible) {
    registry.dispatch(external_gc_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', true);
  }

  dispatch({
    type: 'ENABLE_COMPLEMENTARY_AREA',
    scope,
    area
  });
};
/**
 * Disable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 */

const disableComplementaryArea = scope => ({
  registry
}) => {
  const isComplementaryAreaVisible = registry.select(external_gc_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (isComplementaryAreaVisible) {
    registry.dispatch(external_gc_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', false);
  }
};
/**
 * Pins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 *
 * @return {Object} Action object.
 */

const pinItem = (scope, item) => ({
  registry
}) => {
  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_gc_preferences_namespaceObject.store).get(scope, 'pinnedItems'); // The item is already pinned, there's nothing to do.

  if (pinnedItems?.[item] === true) {
    return;
  }

  registry.dispatch(external_gc_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: true
  });
};
/**
 * Unpins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 */

const unpinItem = (scope, item) => ({
  registry
}) => {
  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_gc_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  registry.dispatch(external_gc_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: false
  });
};
/**
 * Returns an action object used in signalling that a feature should be toggled.
 *
 * @param {string} scope       The feature scope (e.g. core/edit-post).
 * @param {string} featureName The feature name.
 */

function toggleFeature(scope, featureName) {
  return function ({
    registry
  }) {
    external_gc_deprecated_default()(`dispatch( 'core/interface' ).toggleFeature`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).toggle`
    });
    registry.dispatch(external_gc_preferences_namespaceObject.store).toggle(scope, featureName);
  };
}
/**
 * Returns an action object used in signalling that a feature should be set to
 * a true or false value
 *
 * @param {string}  scope       The feature scope (e.g. core/edit-post).
 * @param {string}  featureName The feature name.
 * @param {boolean} value       The value to set.
 *
 * @return {Object} Action object.
 */

function setFeatureValue(scope, featureName, value) {
  return function ({
    registry
  }) {
    external_gc_deprecated_default()(`dispatch( 'core/interface' ).setFeatureValue`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).set`
    });
    registry.dispatch(external_gc_preferences_namespaceObject.store).set(scope, featureName, !!value);
  };
}
/**
 * Returns an action object used in signalling that defaults should be set for features.
 *
 * @param {string}                  scope    The feature scope (e.g. core/edit-post).
 * @param {Object<string, boolean>} defaults A key/value map of feature names to values.
 *
 * @return {Object} Action object.
 */

function setFeatureDefaults(scope, defaults) {
  return function ({
    registry
  }) {
    external_gc_deprecated_default()(`dispatch( 'core/interface' ).setFeatureDefaults`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).setDefaults`
    });
    registry.dispatch(external_gc_preferences_namespaceObject.store).setDefaults(scope, defaults);
  };
}
/**
 * Returns an action object used in signalling that the user opened a modal.
 *
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

function openModal(name) {
  return {
    type: 'OPEN_MODAL',
    name
  };
}
/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @return {Object} Action object.
 */

function closeModal() {
  return {
    type: 'CLOSE_MODAL'
  };
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/selectors.js
/**
 * GeChiUI dependencies
 */



/**
 * Returns the complementary area that is active in a given scope.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Item scope.
 *
 * @return {string | null | undefined} The complementary area that is active in the given scope.
 */

const getActiveComplementaryArea = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, scope) => {
  const isComplementaryAreaVisible = select(external_gc_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible'); // Return `undefined` to indicate that the user has never toggled
  // visibility, this is the vanilla default. Other code relies on this
  // nuance in the return value.

  if (isComplementaryAreaVisible === undefined) {
    return undefined;
  } // Return `null` to indicate the user hid the complementary area.


  if (isComplementaryAreaVisible === false) {
    return null;
  }

  return state?.complementaryAreas?.[scope];
});
const isComplementaryAreaLoading = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, scope) => {
  const isVisible = select(external_gc_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');
  const identifier = state?.complementaryAreas?.[scope];
  return isVisible && identifier === undefined;
});
/**
 * Returns a boolean indicating if an item is pinned or not.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Scope.
 * @param {string} item  Item to check.
 *
 * @return {boolean} True if the item is pinned and false otherwise.
 */

const isItemPinned = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, scope, item) => {
  var _pinnedItems$item;

  const pinnedItems = select(external_gc_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  return (_pinnedItems$item = pinnedItems?.[item]) !== null && _pinnedItems$item !== void 0 ? _pinnedItems$item : true;
});
/**
 * Returns a boolean indicating whether a feature is active for a particular
 * scope.
 *
 * @param {Object} state       The store state.
 * @param {string} scope       The scope of the feature (e.g. core/edit-post).
 * @param {string} featureName The name of the feature.
 *
 * @return {boolean} Is the feature enabled?
 */

const isFeatureActive = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, scope, featureName) => {
  external_gc_deprecated_default()(`select( 'core/interface' ).isFeatureActive( scope, featureName )`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get( scope, featureName )`
  });
  return !!select(external_gc_preferences_namespaceObject.store).get(scope, featureName);
});
/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @param {Object} state     Global application state.
 * @param {string} modalName A string that uniquely identifies the modal.
 *
 * @return {boolean} Whether the modal is active.
 */

function isModalActive(state, modalName) {
  return state.activeModal === modalName;
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/reducer.js
/**
 * GeChiUI dependencies
 */

function complementaryAreas(state = {}, action) {
  switch (action.type) {
    case 'SET_DEFAULT_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action; // If there's already an area, don't overwrite it.

        if (state[scope]) {
          return state;
        }

        return { ...state,
          [scope]: area
        };
      }

    case 'ENABLE_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action;
        return { ...state,
          [scope]: area
        };
      }
  }

  return state;
}
/**
 * Reducer for storing the name of the open modal, or null if no modal is open.
 *
 * @param {Object} state  Previous state.
 * @param {Object} action Action object containing the `name` of the modal
 *
 * @return {Object} Updated state
 */

function activeModal(state = null, action) {
  switch (action.type) {
    case 'OPEN_MODAL':
      return action.name;

    case 'CLOSE_MODAL':
      return null;
  }

  return state;
}
/* harmony default export */ var reducer = ((0,external_gc_data_namespaceObject.combineReducers)({
  complementaryAreas,
  activeModal
}));

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/interface';

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/index.js
/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Store definition for the interface namespace.
 *
 * @see https://github.com/GeChiUI/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_gc_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

(0,external_gc_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-context/index.js
/**
 * GeChiUI dependencies
 */

/* harmony default export */ var complementary_area_context = ((0,external_gc_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon,
    identifier: ownProps.identifier || `${context.name}/${ownProps.name}`
  };
}));

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-toggle/index.js


/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */




function ComplementaryAreaToggle({
  as = external_gc_components_namespaceObject.Button,
  scope,
  identifier,
  icon,
  selectedIcon,
  name,
  ...props
}) {
  const ComponentToUse = as;
  const isSelected = (0,external_gc_data_namespaceObject.useSelect)(select => select(store).getActiveComplementaryArea(scope) === identifier, [identifier]);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);
  return (0,external_gc_element_namespaceObject.createElement)(ComponentToUse, {
    icon: selectedIcon && isSelected ? selectedIcon : icon,
    onClick: () => {
      if (isSelected) {
        disableComplementaryArea(scope);
      } else {
        enableComplementaryArea(scope, identifier);
      }
    },
    ...props
  });
}

/* harmony default export */ var complementary_area_toggle = (complementary_area_context(ComplementaryAreaToggle));

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-header/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */



const ComplementaryAreaHeader = ({
  smallScreenTitle,
  children,
  className,
  toggleButtonProps
}) => {
  const toggleButton = (0,external_gc_element_namespaceObject.createElement)(complementary_area_toggle, {
    icon: close_small,
    ...toggleButtonProps
  });
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "components-panel__header interface-complementary-area-header__small"
  }, smallScreenTitle && (0,external_gc_element_namespaceObject.createElement)("span", {
    className: "interface-complementary-area-header__small-title"
  }, smallScreenTitle), toggleButton), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: classnames_default()('components-panel__header', 'interface-complementary-area-header', className),
    tabIndex: -1
  }, children, toggleButton));
};

/* harmony default export */ var complementary_area_header = (ComplementaryAreaHeader);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/action-item/index.js


/**
 * GeChiUI dependencies
 */



const noop = () => {};

function ActionItemSlot({
  name,
  as: Component = external_gc_components_namespaceObject.ButtonGroup,
  fillProps = {},
  bubblesVirtually,
  ...props
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Slot, {
    name: name,
    bubblesVirtually: bubblesVirtually,
    fillProps: fillProps
  }, fills => {
    if (!external_gc_element_namespaceObject.Children.toArray(fills).length) {
      return null;
    } // Special handling exists for backward compatibility.
    // It ensures that menu items created by plugin authors aren't
    // duplicated with automatically injected menu items coming
    // from pinnable plugin sidebars.
    // @see https://github.com/GeChiUI/gutenberg/issues/14457


    const initializedByPlugins = [];
    external_gc_element_namespaceObject.Children.forEach(fills, ({
      props: {
        __unstableExplicitMenuItem,
        __unstableTarget
      }
    }) => {
      if (__unstableTarget && __unstableExplicitMenuItem) {
        initializedByPlugins.push(__unstableTarget);
      }
    });
    const children = external_gc_element_namespaceObject.Children.map(fills, child => {
      if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(child.props.__unstableTarget)) {
        return null;
      }

      return child;
    });
    return (0,external_gc_element_namespaceObject.createElement)(Component, { ...props
    }, children);
  });
}

function ActionItem({
  name,
  as: Component = external_gc_components_namespaceObject.Button,
  onClick,
  ...props
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Fill, {
    name: name
  }, ({
    onClick: fpOnClick
  }) => {
    return (0,external_gc_element_namespaceObject.createElement)(Component, {
      onClick: onClick || fpOnClick ? (...args) => {
        (onClick || noop)(...args);
        (fpOnClick || noop)(...args);
      } : undefined,
      ...props
    });
  });
}

ActionItem.Slot = ActionItemSlot;
/* harmony default export */ var action_item = (ActionItem);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-more-menu-item/index.js


/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */




const PluginsMenuItem = ({
  // Menu item is marked with unstable prop for backward compatibility.
  // They are removed so they don't leak to DOM elements.
  // @see https://github.com/GeChiUI/gutenberg/issues/14457
  __unstableExplicitMenuItem,
  __unstableTarget,
  ...restProps
}) => (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, { ...restProps
});

function ComplementaryAreaMoreMenuItem({
  scope,
  target,
  __unstableExplicitMenuItem,
  ...props
}) {
  return (0,external_gc_element_namespaceObject.createElement)(complementary_area_toggle, {
    as: toggleProps => {
      return (0,external_gc_element_namespaceObject.createElement)(action_item, {
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`,
        ...toggleProps
      });
    },
    role: "menuitemcheckbox",
    selectedIcon: library_check,
    name: target,
    scope: scope,
    ...props
  });
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/pinned-items/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */



function PinnedItems({
  scope,
  ...props
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Fill, {
    name: `PinnedItems/${scope}`,
    ...props
  });
}

function PinnedItemsSlot({
  scope,
  className,
  ...props
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Slot, {
    name: `PinnedItems/${scope}`,
    ...props
  }, fills => fills?.length > 0 && (0,external_gc_element_namespaceObject.createElement)("div", {
    className: classnames_default()(className, 'interface-pinned-items')
  }, fills));
}

PinnedItems.Slot = PinnedItemsSlot;
/* harmony default export */ var pinned_items = (PinnedItems);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */








function ComplementaryAreaSlot({
  scope,
  ...props
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Slot, {
    name: `ComplementaryArea/${scope}`,
    ...props
  });
}

function ComplementaryAreaFill({
  scope,
  children,
  className
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Fill, {
    name: `ComplementaryArea/${scope}`
  }, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: className
  }, children));
}

function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
  const previousIsSmall = (0,external_gc_element_namespaceObject.useRef)(false);
  const shouldOpenWhenNotSmall = (0,external_gc_element_namespaceObject.useRef)(false);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    // If the complementary area is active and the editor is switching from
    // a big to a small window size.
    if (isActive && isSmall && !previousIsSmall.current) {
      disableComplementaryArea(scope); // Flag the complementary area to be reopened when the window size
      // goes from small to big.

      shouldOpenWhenNotSmall.current = true;
    } else if ( // If there is a flag indicating the complementary area should be
    // enabled when we go from small to big window size and we are going
    // from a small to big window size.
    shouldOpenWhenNotSmall.current && !isSmall && previousIsSmall.current) {
      // Remove the flag indicating the complementary area should be
      // enabled.
      shouldOpenWhenNotSmall.current = false;
      enableComplementaryArea(scope, identifier);
    } else if ( // If the flag is indicating the current complementary should be
    // reopened but another complementary area becomes active, remove
    // the flag.
    shouldOpenWhenNotSmall.current && activeArea && activeArea !== identifier) {
      shouldOpenWhenNotSmall.current = false;
    }

    if (isSmall !== previousIsSmall.current) {
      previousIsSmall.current = isSmall;
    }
  }, [isActive, isSmall, scope, identifier, activeArea]);
}

function ComplementaryArea({
  children,
  className,
  closeLabel = (0,external_gc_i18n_namespaceObject.__)('关闭插件'),
  identifier,
  header,
  headerClassName,
  icon,
  isPinnable = true,
  panelClassName,
  scope,
  name,
  smallScreenTitle,
  title,
  toggleShortcut,
  isActiveByDefault,
  showIconLabels = false
}) {
  const {
    isLoading,
    isActive,
    isPinned,
    activeArea,
    isSmall,
    isLarge
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getActiveComplementaryArea,
      isComplementaryAreaLoading,
      isItemPinned
    } = select(store);

    const _activeArea = getActiveComplementaryArea(scope);

    return {
      isLoading: isComplementaryAreaLoading(scope),
      isActive: _activeArea === identifier,
      isPinned: isItemPinned(scope, identifier),
      activeArea: _activeArea,
      isSmall: select(external_gc_viewport_namespaceObject.store).isViewportMatch('< medium'),
      isLarge: select(external_gc_viewport_namespaceObject.store).isViewportMatch('large')
    };
  }, [identifier, scope]);
  useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall);
  const {
    enableComplementaryArea,
    disableComplementaryArea,
    pinItem,
    unpinItem
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    // Set initial visibility: For large screens, enable if it's active by
    // default. For small screens, always initially disable.
    if (isActiveByDefault && activeArea === undefined && !isSmall) {
      enableComplementaryArea(scope, identifier);
    } else if (activeArea === undefined && isSmall) {
      disableComplementaryArea(scope, identifier);
    }
  }, [activeArea, isActiveByDefault, scope, identifier, isSmall]);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, isPinnable && (0,external_gc_element_namespaceObject.createElement)(pinned_items, {
    scope: scope
  }, isPinned && (0,external_gc_element_namespaceObject.createElement)(complementary_area_toggle, {
    scope: scope,
    identifier: identifier,
    isPressed: isActive && (!showIconLabels || isLarge),
    "aria-expanded": isActive,
    "aria-disabled": isLoading,
    label: title,
    icon: showIconLabels ? library_check : icon,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  })), name && isPinnable && (0,external_gc_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem, {
    target: name,
    scope: scope,
    icon: icon
  }, title), isActive && (0,external_gc_element_namespaceObject.createElement)(ComplementaryAreaFill, {
    className: classnames_default()('interface-complementary-area', className),
    scope: scope
  }, (0,external_gc_element_namespaceObject.createElement)(complementary_area_header, {
    className: headerClassName,
    closeLabel: closeLabel,
    onClose: () => disableComplementaryArea(scope),
    smallScreenTitle: smallScreenTitle,
    toggleButtonProps: {
      label: closeLabel,
      shortcut: toggleShortcut,
      scope,
      identifier
    }
  }, header || (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)("strong", null, title), isPinnable && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "interface-complementary-area__pin-unpin-item",
    icon: isPinned ? star_filled : star_empty,
    label: isPinned ? (0,external_gc_i18n_namespaceObject.__)('从工具栏移除') : (0,external_gc_i18n_namespaceObject.__)('固定至工具栏'),
    onClick: () => (isPinned ? unpinItem : pinItem)(scope, identifier),
    isPressed: isPinned,
    "aria-expanded": isPinned
  }))), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Panel, {
    className: panelClassName
  }, children)));
}

const ComplementaryAreaWrapped = complementary_area_context(ComplementaryArea);
ComplementaryAreaWrapped.Slot = ComplementaryAreaSlot;
/* harmony default export */ var complementary_area = (ComplementaryAreaWrapped);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/fullscreen-mode/index.js
/**
 * GeChiUI dependencies
 */


const FullscreenMode = ({
  isActive
}) => {
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    let isSticky = false; // `is-fullscreen-mode` is set in PHP as a body class by Gutenberg, and this causes
    // `sticky-menu` to be applied by GeChiUI and prevents the admin menu being scrolled
    // even if `is-fullscreen-mode` is then removed. Let's remove `sticky-menu` here as
    // a consequence of the FullscreenMode setup.

    if (document.body.classList.contains('sticky-menu')) {
      isSticky = true;
      document.body.classList.remove('sticky-menu');
    }

    return () => {
      if (isSticky) {
        document.body.classList.add('sticky-menu');
      }
    };
  }, []);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (isActive) {
      document.body.classList.add('is-fullscreen-mode');
    } else {
      document.body.classList.remove('is-fullscreen-mode');
    }

    return () => {
      if (isActive) {
        document.body.classList.remove('is-fullscreen-mode');
      }
    };
  }, [isActive]);
  return null;
};

/* harmony default export */ var fullscreen_mode = (FullscreenMode);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/navigable-region/index.js


/**
 * External dependencies
 */

function NavigableRegion({
  children,
  className,
  ariaLabel,
  as: Tag = 'div',
  ...props
}) {
  return (0,external_gc_element_namespaceObject.createElement)(Tag, {
    className: classnames_default()('interface-navigable-region', className),
    "aria-label": ariaLabel,
    role: "region",
    tabIndex: "-1",
    ...props
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/interface-skeleton/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */



function useHTMLClass(className) {
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    const element = document && document.querySelector(`html:not(.${className})`);

    if (!element) {
      return;
    }

    element.classList.toggle(className);
    return () => {
      element.classList.toggle(className);
    };
  }, [className]);
}

const headerVariants = {
  hidden: {
    opacity: 0
  },
  hover: {
    opacity: 1,
    transition: {
      type: 'tween',
      delay: 0.2,
      delayChildren: 0.2
    }
  },
  distractionFreeInactive: {
    opacity: 1,
    transition: {
      delay: 0
    }
  }
};

function InterfaceSkeleton({
  isDistractionFree,
  footer,
  header,
  editorNotices,
  sidebar,
  secondarySidebar,
  notices,
  content,
  actions,
  labels,
  className,
  enableRegionNavigation = true,
  // Todo: does this need to be a prop.
  // Can we use a dependency to keyboard-shortcuts directly?
  shortcuts
}, ref) {
  const navigateRegionsProps = (0,external_gc_components_namespaceObject.__unstableUseNavigateRegions)(shortcuts);
  useHTMLClass('interface-interface-skeleton__html-container');
  const defaultLabels = {
    /* translators: accessibility text for the top bar landmark region. */
    header: (0,external_gc_i18n_namespaceObject.__)('页眉'),

    /* translators: accessibility text for the content landmark region. */
    body: (0,external_gc_i18n_namespaceObject.__)('内容'),

    /* translators: accessibility text for the secondary sidebar landmark region. */
    secondarySidebar: (0,external_gc_i18n_namespaceObject.__)('区块库'),

    /* translators: accessibility text for the settings landmark region. */
    sidebar: (0,external_gc_i18n_namespaceObject.__)('设置'),

    /* translators: accessibility text for the publish landmark region. */
    actions: (0,external_gc_i18n_namespaceObject.__)('发布'),

    /* translators: accessibility text for the footer landmark region. */
    footer: (0,external_gc_i18n_namespaceObject.__)('页脚')
  };
  const mergedLabels = { ...defaultLabels,
    ...labels
  };
  return (0,external_gc_element_namespaceObject.createElement)("div", { ...(enableRegionNavigation ? navigateRegionsProps : {}),
    ref: (0,external_gc_compose_namespaceObject.useMergeRefs)([ref, enableRegionNavigation ? navigateRegionsProps.ref : undefined]),
    className: classnames_default()(className, 'interface-interface-skeleton', navigateRegionsProps.className, !!footer && 'has-footer')
  }, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__editor"
  }, !!header && (0,external_gc_element_namespaceObject.createElement)(NavigableRegion, {
    as: external_gc_components_namespaceObject.__unstableMotion.div,
    className: "interface-interface-skeleton__header",
    "aria-label": mergedLabels.header,
    initial: isDistractionFree ? 'hidden' : 'distractionFreeInactive',
    whileHover: isDistractionFree ? 'hover' : 'distractionFreeInactive',
    animate: isDistractionFree ? 'hidden' : 'distractionFreeInactive',
    variants: headerVariants,
    transition: isDistractionFree ? {
      type: 'tween',
      delay: 0.8
    } : undefined
  }, header), isDistractionFree && (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__header"
  }, editorNotices), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__body"
  }, !!secondarySidebar && (0,external_gc_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__secondary-sidebar",
    ariaLabel: mergedLabels.secondarySidebar
  }, secondarySidebar), !!notices && (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__notices"
  }, notices), (0,external_gc_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__content",
    ariaLabel: mergedLabels.body
  }, content), !!sidebar && (0,external_gc_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__sidebar",
    ariaLabel: mergedLabels.sidebar
  }, sidebar), !!actions && (0,external_gc_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__actions",
    ariaLabel: mergedLabels.actions
  }, actions))), !!footer && (0,external_gc_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__footer",
    ariaLabel: mergedLabels.footer
  }, footer));
}

/* harmony default export */ var interface_skeleton = ((0,external_gc_element_namespaceObject.forwardRef)(InterfaceSkeleton));

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/more-vertical.js


/**
 * GeChiUI dependencies
 */

const moreVertical = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ var more_vertical = (moreVertical);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/more-menu-dropdown/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */




function MoreMenuDropdown({
  as: DropdownComponent = external_gc_components_namespaceObject.DropdownMenu,
  className,

  /* translators: button label text should, if possible, be under 16 characters. */
  label = (0,external_gc_i18n_namespaceObject.__)('选项'),
  popoverProps,
  toggleProps,
  children
}) {
  return (0,external_gc_element_namespaceObject.createElement)(DropdownComponent, {
    className: classnames_default()('interface-more-menu-dropdown', className),
    icon: more_vertical,
    label: label,
    popoverProps: {
      placement: 'bottom-end',
      ...popoverProps,
      className: classnames_default()('interface-more-menu-dropdown__content', popoverProps?.className)
    },
    toggleProps: {
      tooltipPosition: 'bottom',
      ...toggleProps
    }
  }, onClose => children(onClose));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/preferences-modal/index.js


/**
 * GeChiUI dependencies
 */


function PreferencesModal({
  closeModal,
  children
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Modal, {
    className: "interface-preferences-modal",
    title: (0,external_gc_i18n_namespaceObject.__)('偏好设置'),
    onRequestClose: closeModal
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/icon/index.js
/**
 * GeChiUI dependencies
 */

/** @typedef {{icon: JSX.Element, size?: number} & import('@gechiui/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps} props icon is the SVG component to render
 *                          size is a number specifiying the icon size in pixels
 *                          Other props will be passed to wrapped SVG component
 *
 * @return {JSX.Element}  Icon component
 */

function Icon({
  icon,
  size = 24,
  ...props
}) {
  return (0,external_gc_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ var icon = (Icon);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/chevron-left.js


/**
 * GeChiUI dependencies
 */

const chevronLeft = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"
}));
/* harmony default export */ var chevron_left = (chevronLeft);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/chevron-right.js


/**
 * GeChiUI dependencies
 */

const chevronRight = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"
}));
/* harmony default export */ var chevron_right = (chevronRight);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/preferences-modal-tabs/index.js


/**
 * GeChiUI dependencies
 */





const PREFERENCES_MENU = 'preferences-menu';
function PreferencesModalTabs({
  sections
}) {
  const isLargeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('medium'); // This is also used to sync the two different rendered components
  // between small and large viewports.

  const [activeMenu, setActiveMenu] = (0,external_gc_element_namespaceObject.useState)(PREFERENCES_MENU);
  /**
   * Create helper objects from `sections` for easier data handling.
   * `tabs` is used for creating the `TabPanel` and `sectionsContentMap`
   * is used for easier access to active tab's content.
   */

  const {
    tabs,
    sectionsContentMap
  } = (0,external_gc_element_namespaceObject.useMemo)(() => {
    let mappedTabs = {
      tabs: [],
      sectionsContentMap: {}
    };

    if (sections.length) {
      mappedTabs = sections.reduce((accumulator, {
        name,
        tabLabel: title,
        content
      }) => {
        accumulator.tabs.push({
          name,
          title
        });
        accumulator.sectionsContentMap[name] = content;
        return accumulator;
      }, {
        tabs: [],
        sectionsContentMap: {}
      });
    }

    return mappedTabs;
  }, [sections]);
  const getCurrentTab = (0,external_gc_element_namespaceObject.useCallback)(tab => sectionsContentMap[tab.name] || null, [sectionsContentMap]);
  let modalContent; // We render different components based on the viewport size.

  if (isLargeViewport) {
    modalContent = (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.TabPanel, {
      className: "interface-preferences__tabs",
      tabs: tabs,
      initialTabName: activeMenu !== PREFERENCES_MENU ? activeMenu : undefined,
      onSelect: setActiveMenu,
      orientation: "vertical"
    }, getCurrentTab);
  } else {
    modalContent = (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalNavigatorProvider, {
      initialPath: "/",
      className: "interface-preferences__provider"
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalNavigatorScreen, {
      path: "/"
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Card, {
      isBorderless: true,
      size: "small"
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.CardBody, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalItemGroup, null, tabs.map(tab => {
      return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalNavigatorButton, {
        key: tab.name,
        path: tab.name,
        as: external_gc_components_namespaceObject.__experimentalItem,
        isAction: true
      }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalHStack, {
        justify: "space-between"
      }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.FlexItem, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalTruncate, null, tab.title)), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.FlexItem, null, (0,external_gc_element_namespaceObject.createElement)(icon, {
        icon: (0,external_gc_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
      }))));
    }))))), sections.length && sections.map(section => {
      return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalNavigatorScreen, {
        key: `${section.name}-menu`,
        path: section.name
      }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Card, {
        isBorderless: true,
        size: "large"
      }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.CardHeader, {
        isBorderless: false,
        justify: "left",
        size: "small",
        gap: "6"
      }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalNavigatorBackButton, {
        icon: (0,external_gc_i18n_namespaceObject.isRTL)() ? chevron_right : chevron_left,
        "aria-label": (0,external_gc_i18n_namespaceObject.__)('导航至上一视图')
      }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalText, {
        size: "16"
      }, section.tabLabel)), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.CardBody, null, section.content)));
    }));
  }

  return modalContent;
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/preferences-modal-section/index.js


const Section = ({
  description,
  title,
  children
}) => (0,external_gc_element_namespaceObject.createElement)("fieldset", {
  className: "interface-preferences-modal__section"
}, (0,external_gc_element_namespaceObject.createElement)("legend", {
  className: "interface-preferences-modal__section-legend"
}, (0,external_gc_element_namespaceObject.createElement)("h2", {
  className: "interface-preferences-modal__section-title"
}, title), description && (0,external_gc_element_namespaceObject.createElement)("p", {
  className: "interface-preferences-modal__section-description"
}, description)), children);

/* harmony default export */ var preferences_modal_section = (Section);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/preferences-modal-base-option/index.js


/**
 * GeChiUI dependencies
 */


function BaseOption({
  help,
  label,
  isChecked,
  onChange,
  children
}) {
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "interface-preferences-modal__option"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ToggleControl, {
    __nextHasNoMarginBottom: true,
    help: help,
    label: label,
    checked: isChecked,
    onChange: onChange
  }), children);
}

/* harmony default export */ var preferences_modal_base_option = (BaseOption);

;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/index.js














;// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/index.js



;// CONCATENATED MODULE: external ["gc","keyboardShortcuts"]
var external_gc_keyboardShortcuts_namespaceObject = window["gc"]["keyboardShortcuts"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * GeChiUI dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('将选中的文字加粗。')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('将选中的文字设为斜体。')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('将选中的文字转换为链接。')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('移除链接。')
}, {
  keyCombination: {
    character: '[['
  },
  description: (0,external_gc_i18n_namespaceObject.__)('插入一个指向文章或页面的链接。')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('给选中的文字加下划线。')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'd'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('为所选文本添加删除线。')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'x'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('将选定的文本设为内联代码。')
}, {
  keyCombination: {
    modifier: 'access',
    character: '0'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('将当前标题转换为段落。')
}, {
  keyCombination: {
    modifier: 'access',
    character: '1-6'
  },
  description: (0,external_gc_i18n_namespaceObject.__)('将当前段落或标题转换为级别为h1到h6的标题。')
}];

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * GeChiUI dependencies
 */



function KeyCombination({
  keyCombination,
  forceAriaLabel
}) {
  const shortcut = keyCombination.modifier ? external_gc_keycodes_namespaceObject.displayShortcutList[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_gc_keycodes_namespaceObject.shortcutAriaLabel[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return (0,external_gc_element_namespaceObject.createElement)("kbd", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, (Array.isArray(shortcut) ? shortcut : [shortcut]).map((character, index) => {
    if (character === '+') {
      return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, {
        key: index
      }, character);
    }

    return (0,external_gc_element_namespaceObject.createElement)("kbd", {
      key: index,
      className: "edit-post-keyboard-shortcut-help-modal__shortcut-key"
    }, character);
  }));
}

function Shortcut({
  description,
  keyCombination,
  aliases = [],
  ariaLabel
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-description"
  }, description), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-term"
  }, (0,external_gc_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map((alias, index) => (0,external_gc_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: alias,
    forceAriaLabel: ariaLabel,
    key: index
  }))));
}

/* harmony default export */ var keyboard_shortcut_help_modal_shortcut = (Shortcut);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */



function DynamicShortcut({
  name
}) {
  const {
    keyCombination,
    description,
    aliases
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getShortcutKeyCombination,
      getShortcutDescription,
      getShortcutAliases
    } = select(external_gc_keyboardShortcuts_namespaceObject.store);
    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  }, [name]);

  if (!keyCombination) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

/* harmony default export */ var dynamic_shortcut = (DynamicShortcut);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */




const KEYBOARD_SHORTCUT_HELP_MODAL_NAME = 'edit-post/keyboard-shortcut-help';

const ShortcutList = ({
  shortcuts
}) =>
/*
 * Disable reason: The `list` ARIA role is redundant but
 * Safari+VoiceOver won't announce the list otherwise.
 */

/* eslint-disable jsx-a11y/no-redundant-roles */
(0,external_gc_element_namespaceObject.createElement)("ul", {
  className: "edit-post-keyboard-shortcut-help-modal__shortcut-list",
  role: "list"
}, shortcuts.map((shortcut, index) => (0,external_gc_element_namespaceObject.createElement)("li", {
  className: "edit-post-keyboard-shortcut-help-modal__shortcut",
  key: index
}, typeof shortcut === 'string' ? (0,external_gc_element_namespaceObject.createElement)(dynamic_shortcut, {
  name: shortcut
}) : (0,external_gc_element_namespaceObject.createElement)(keyboard_shortcut_help_modal_shortcut, { ...shortcut
}))))
/* eslint-enable jsx-a11y/no-redundant-roles */
;

const ShortcutSection = ({
  title,
  shortcuts,
  className
}) => (0,external_gc_element_namespaceObject.createElement)("section", {
  className: classnames_default()('edit-post-keyboard-shortcut-help-modal__section', className)
}, !!title && (0,external_gc_element_namespaceObject.createElement)("h2", {
  className: "edit-post-keyboard-shortcut-help-modal__section-title"
}, title), (0,external_gc_element_namespaceObject.createElement)(ShortcutList, {
  shortcuts: shortcuts
}));

const ShortcutCategorySection = ({
  title,
  categoryName,
  additionalShortcuts = []
}) => {
  const categoryShortcuts = (0,external_gc_data_namespaceObject.useSelect)(select => {
    return select(external_gc_keyboardShortcuts_namespaceObject.store).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return (0,external_gc_element_namespaceObject.createElement)(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal({
  isModalActive,
  toggleModal
}) {
  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/keyboard-shortcuts', toggleModal);

  if (!isModalActive) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Modal, {
    className: "edit-post-keyboard-shortcut-help-modal",
    title: (0,external_gc_i18n_namespaceObject.__)('键盘快捷键'),
    closeButtonLabel: (0,external_gc_i18n_namespaceObject.__)('关闭'),
    onRequestClose: toggleModal
  }, (0,external_gc_element_namespaceObject.createElement)(ShortcutSection, {
    className: "edit-post-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/edit-post/keyboard-shortcuts']
  }), (0,external_gc_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_gc_i18n_namespaceObject.__)('全局快捷键'),
    categoryName: "global"
  }), (0,external_gc_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_gc_i18n_namespaceObject.__)('选定快捷键'),
    categoryName: "selection"
  }), (0,external_gc_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_gc_i18n_namespaceObject.__)('区块快捷键'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: (0,external_gc_i18n_namespaceObject.__)('在添加新段落后修改区块类型。'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: (0,external_gc_i18n_namespaceObject.__)('正斜杠')
    }]
  }), (0,external_gc_element_namespaceObject.createElement)(ShortcutSection, {
    title: (0,external_gc_i18n_namespaceObject.__)('文字内容格式化'),
    shortcuts: textFormattingShortcuts
  }));
}
/* harmony default export */ var keyboard_shortcut_help_modal = ((0,external_gc_compose_namespaceObject.compose)([(0,external_gc_data_namespaceObject.withSelect)(select => ({
  isModalActive: select(store).isModalActive(KEYBOARD_SHORTCUT_HELP_MODAL_NAME)
})), (0,external_gc_data_namespaceObject.withDispatch)((dispatch, {
  isModalActive
}) => {
  const {
    openModal,
    closeModal
  } = dispatch(store);
  return {
    toggleModal: () => isModalActive ? closeModal() : openModal(KEYBOARD_SHORTCUT_HELP_MODAL_NAME)
  };
})])(KeyboardShortcutHelpModal));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


function KeyboardShortcutsHelpMenuItem({
  openModal
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    onClick: () => {
      openModal(KEYBOARD_SHORTCUT_HELP_MODAL_NAME);
    },
    shortcut: external_gc_keycodes_namespaceObject.displayShortcut.access('h')
  }, (0,external_gc_i18n_namespaceObject.__)('键盘快捷键'));
}
/* harmony default export */ var keyboard_shortcuts_help_menu_item = ((0,external_gc_data_namespaceObject.withDispatch)(dispatch => {
  const {
    openModal
  } = dispatch(store);
  return {
    openModal
  };
})(KeyboardShortcutsHelpMenuItem));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/tools-more-menu-group/index.js


/**
 * GeChiUI dependencies
 */


const {
  Fill: ToolsMoreMenuGroup,
  Slot
} = (0,external_gc_components_namespaceObject.createSlotFill)('ToolsMoreMenuGroup');

ToolsMoreMenuGroup.Slot = ({
  fillProps
}) => (0,external_gc_element_namespaceObject.createElement)(Slot, {
  fillProps: fillProps
}, fills => fills.length > 0 && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuGroup, {
  label: (0,external_gc_i18n_namespaceObject.__)('工具')
}, fills));

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/plugins/index.js


/**
 * GeChiUI dependencies
 */








/**
 * Internal dependencies
 */





function ManagePatternsMenuItem() {
  const url = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      canUser
    } = select(external_gc_coreData_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_gc_editor_namespaceObject.store);

    const isBlockTheme = getEditorSettings().__unstableIsBlockBasedTheme;

    const defaultUrl = (0,external_gc_url_namespaceObject.addQueryArgs)('edit.php', {
      post_type: 'gc_block'
    });
    const patternsUrl = (0,external_gc_url_namespaceObject.addQueryArgs)('site-editor.php', {
      path: '/patterns'
    }); // The site editor and templates both check whether the user has
    // edit_theme_options capabilities. We can leverage that here and not
    // display the manage patterns link if the user can't access it.

    return canUser('read', 'templates') && isBlockTheme ? patternsUrl : defaultUrl;
  }, []);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    role: "menuitem",
    href: url
  }, (0,external_gc_i18n_namespaceObject.__)('管理样板'));
}

(0,external_gc_plugins_namespaceObject.registerPlugin)('edit-post', {
  render() {
    return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(tools_more_menu_group, null, ({
      onClose
    }) => (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(ManagePatternsMenuItem, null), (0,external_gc_element_namespaceObject.createElement)(keyboard_shortcuts_help_menu_item, {
      onSelect: onClose
    }), (0,external_gc_element_namespaceObject.createElement)(CopyContentMenuItem, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
      role: "menuitem",
      icon: library_external,
      href: (0,external_gc_i18n_namespaceObject.__)('https://www.gechiui.com/support/gechiui-block-editor/'),
      target: "_blank",
      rel: "noopener noreferrer"
    }, (0,external_gc_i18n_namespaceObject.__)('帮助'), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.VisuallyHidden, {
      as: "span"
    },
    /* translators: accessibility text */
    (0,external_gc_i18n_namespaceObject.__)('（在新窗口中打开）'))))));
  }

});

;// CONCATENATED MODULE: external ["gc","commands"]
var external_gc_commands_namespaceObject = window["gc"]["commands"];
;// CONCATENATED MODULE: external ["gc","coreCommands"]
var external_gc_coreCommands_namespaceObject = window["gc"]["coreCommands"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/store/reducer.js
/**
 * GeChiUI dependencies
 */

/**
 * Reducer storing the list of all programmatically removed panels.
 *
 * @param {Array}  state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Array} Updated state.
 */

function removedPanels(state = [], action) {
  switch (action.type) {
    case 'REMOVE_PANEL':
      if (!state.includes(action.panelName)) {
        return [...state, action.panelName];
      }

  }

  return state;
}
function publishSidebarActive(state = false, action) {
  switch (action.type) {
    case 'OPEN_PUBLISH_SIDEBAR':
      return true;

    case 'CLOSE_PUBLISH_SIDEBAR':
      return false;

    case 'TOGGLE_PUBLISH_SIDEBAR':
      return !state;
  }

  return state;
}
/**
 * Reducer keeping track of the meta boxes isSaving state.
 * A "true" value means the meta boxes saving request is in-flight.
 *
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {Object} Updated state.
 */

function isSavingMetaBoxes(state = false, action) {
  switch (action.type) {
    case 'REQUEST_META_BOX_UPDATES':
      return true;

    case 'META_BOX_UPDATES_SUCCESS':
    case 'META_BOX_UPDATES_FAILURE':
      return false;

    default:
      return state;
  }
}

function mergeMetaboxes(metaboxes = [], newMetaboxes) {
  const mergedMetaboxes = [...metaboxes];

  for (const metabox of newMetaboxes) {
    const existing = mergedMetaboxes.findIndex(box => box.id === metabox.id);

    if (existing !== -1) {
      mergedMetaboxes[existing] = metabox;
    } else {
      mergedMetaboxes.push(metabox);
    }
  }

  return mergedMetaboxes;
}
/**
 * Reducer keeping track of the meta boxes per location.
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {Object} Updated state.
 */


function metaBoxLocations(state = {}, action) {
  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      {
        const newState = { ...state
        };

        for (const [location, metaboxes] of Object.entries(action.metaBoxesPerLocation)) {
          newState[location] = mergeMetaboxes(newState[location], metaboxes);
        }

        return newState;
      }
  }

  return state;
}
/**
 * Reducer returning the editing canvas device type.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function deviceType(state = 'Desktop', action) {
  switch (action.type) {
    case 'SET_PREVIEW_DEVICE_TYPE':
      return action.deviceType;
  }

  return state;
}
/**
 * Reducer to set the block inserter panel open or closed.
 *
 * Note: this reducer interacts with the list view panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function blockInserterPanel(state = false, action) {
  switch (action.type) {
    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen ? false : state;

    case 'SET_IS_INSERTER_OPENED':
      return action.value;
  }

  return state;
}
/**
 * Reducer to set the list view panel open or closed.
 *
 * Note: this reducer interacts with the inserter panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function listViewPanel(state = false, action) {
  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value ? false : state;

    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen;
  }

  return state;
}
/**
 * Reducer tracking whether template editing is on or off.
 *
 * @param {boolean} state
 * @param {Object}  action
 */

function isEditingTemplate(state = false, action) {
  switch (action.type) {
    case 'SET_IS_EDITING_TEMPLATE':
      return action.value;
  }

  return state;
}
/**
 * Reducer tracking whether meta boxes are initialized.
 *
 * @param {boolean} state
 * @param {Object}  action
 *
 * @return {boolean} Updated state.
 */


function metaBoxesInitialized(state = false, action) {
  switch (action.type) {
    case 'META_BOXES_INITIALIZED':
      return true;
  }

  return state;
}

const metaBoxes = (0,external_gc_data_namespaceObject.combineReducers)({
  isSaving: isSavingMetaBoxes,
  locations: metaBoxLocations,
  initialized: metaBoxesInitialized
});
/* harmony default export */ var store_reducer = ((0,external_gc_data_namespaceObject.combineReducers)({
  metaBoxes,
  publishSidebarActive,
  removedPanels,
  deviceType,
  blockInserterPanel,
  listViewPanel,
  isEditingTemplate
}));

;// CONCATENATED MODULE: external ["gc","apiFetch"]
var external_gc_apiFetch_namespaceObject = window["gc"]["apiFetch"];
var external_gc_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_gc_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["gc","a11y"]
var external_gc_a11y_namespaceObject = window["gc"]["a11y"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/utils/meta-boxes.js
/**
 * Function returning the current Meta Boxes DOM Node in the editor
 * whether the meta box area is opened or not.
 * If the MetaBox Area is visible returns it, and returns the original container instead.
 *
 * @param {string} location Meta Box location.
 *
 * @return {string} HTML content.
 */
const getMetaBoxContainer = location => {
  const area = document.querySelector(`.edit-post-meta-boxes-area.is-${location} .metabox-location-${location}`);

  if (area) {
    return area;
  }

  return document.querySelector('#metaboxes .metabox-location-' + location);
};

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/store/actions.js
/**
 * GeChiUI dependencies
 */










/**
 * Internal dependencies
 */



/**
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {?string} name Sidebar name to be opened.
 */

const openGeneralSidebar = name => ({
  registry
}) => registry.dispatch(store).enableComplementaryArea(store_store.name, name);
/**
 * Returns an action object signalling that the user closed the sidebar.
 */

const closeGeneralSidebar = () => ({
  registry
}) => registry.dispatch(store).disableComplementaryArea(store_store.name);
/**
 * Returns an action object used in signalling that the user opened a modal.
 *
 * @deprecated since GC 6.3 use `core/interface` store's action with the same name instead.
 *
 *
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

const actions_openModal = name => ({
  registry
}) => {
  external_gc_deprecated_default()("select( 'core/edit-post' ).openModal( name )", {
    since: '6.3',
    alternative: "select( 'core/interface').openModal( name )"
  });
  return registry.dispatch(store).openModal(name);
};
/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @deprecated since GC 6.3 use `core/interface` store's action with the same name instead.
 *
 * @return {Object} Action object.
 */

const actions_closeModal = () => ({
  registry
}) => {
  external_gc_deprecated_default()("select( 'core/edit-post' ).closeModal()", {
    since: '6.3',
    alternative: "select( 'core/interface').closeModal()"
  });
  return registry.dispatch(store).closeModal();
};
/**
 * Returns an action object used in signalling that the user opened the publish
 * sidebar.
 *
 * @return {Object} Action object
 */

function openPublishSidebar() {
  return {
    type: 'OPEN_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user closed the
 * publish sidebar.
 *
 * @return {Object} Action object.
 */

function closePublishSidebar() {
  return {
    type: 'CLOSE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user toggles the publish sidebar.
 *
 * @return {Object} Action object
 */

function togglePublishSidebar() {
  return {
    type: 'TOGGLE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used to enable or disable a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to enable or disable.
 *
 * @return {Object} Action object.
 */

const toggleEditorPanelEnabled = panelName => ({
  registry
}) => {
  var _registry$select$get;

  const inactivePanels = (_registry$select$get = registry.select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'inactivePanels')) !== null && _registry$select$get !== void 0 ? _registry$select$get : [];
  const isPanelInactive = !!inactivePanels?.includes(panelName); // If the panel is inactive, remove it to enable it, else add it to
  // make it inactive.

  let updatedInactivePanels;

  if (isPanelInactive) {
    updatedInactivePanels = inactivePanels.filter(invactivePanelName => invactivePanelName !== panelName);
  } else {
    updatedInactivePanels = [...inactivePanels, panelName];
  }

  registry.dispatch(external_gc_preferences_namespaceObject.store).set('core/edit-post', 'inactivePanels', updatedInactivePanels);
};
/**
 * Opens a closed panel and closes an open panel.
 *
 * @param {string} panelName A string that identifies the panel to open or close.
 */

const toggleEditorPanelOpened = panelName => ({
  registry
}) => {
  var _registry$select$get2;

  const openPanels = (_registry$select$get2 = registry.select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'openPanels')) !== null && _registry$select$get2 !== void 0 ? _registry$select$get2 : [];
  const isPanelOpen = !!openPanels?.includes(panelName); // If the panel is open, remove it to close it, else add it to
  // make it open.

  let updatedOpenPanels;

  if (isPanelOpen) {
    updatedOpenPanels = openPanels.filter(openPanelName => openPanelName !== panelName);
  } else {
    updatedOpenPanels = [...openPanels, panelName];
  }

  registry.dispatch(external_gc_preferences_namespaceObject.store).set('core/edit-post', 'openPanels', updatedOpenPanels);
};
/**
 * Returns an action object used to remove a panel from the editor.
 *
 * @param {string} panelName A string that identifies the panel to remove.
 *
 * @return {Object} Action object.
 */

function removeEditorPanel(panelName) {
  return {
    type: 'REMOVE_PANEL',
    panelName
  };
}
/**
 * Triggers an action used to toggle a feature flag.
 *
 * @param {string} feature Feature name.
 */

const actions_toggleFeature = feature => ({
  registry
}) => registry.dispatch(external_gc_preferences_namespaceObject.store).toggle('core/edit-post', feature);
/**
 * Triggers an action used to switch editor mode.
 *
 * @param {string} mode The editor mode.
 */

const switchEditorMode = mode => ({
  registry
}) => {
  registry.dispatch(external_gc_preferences_namespaceObject.store).set('core/edit-post', 'editorMode', mode); // Unselect blocks when we switch to the code editor.

  if (mode !== 'visual') {
    registry.dispatch(external_gc_blockEditor_namespaceObject.store).clearSelectedBlock();
  }

  const message = mode === 'visual' ? (0,external_gc_i18n_namespaceObject.__)('已选择可视化编辑器') : (0,external_gc_i18n_namespaceObject.__)('已选择代码编辑器');
  (0,external_gc_a11y_namespaceObject.speak)(message, 'assertive');
};
/**
 * Triggers an action object used to toggle a plugin name flag.
 *
 * @param {string} pluginName Plugin name.
 */

const togglePinnedPluginItem = pluginName => ({
  registry
}) => {
  const isPinned = registry.select(store).isItemPinned('core/edit-post', pluginName);
  registry.dispatch(store)[isPinned ? 'unpinItem' : 'pinItem']('core/edit-post', pluginName);
};
/**
 * Returns an action object used in signaling that a style should be auto-applied when a block is created.
 *
 * @param {string}  blockName  Name of the block.
 * @param {?string} blockStyle Name of the style that should be auto applied. If undefined, the "auto apply" setting of the block is removed.
 */

const updatePreferredStyleVariations = (blockName, blockStyle) => ({
  registry
}) => {
  var _registry$select$get3;

  if (!blockName) {
    return;
  }

  const existingVariations = (_registry$select$get3 = registry.select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'preferredStyleVariations')) !== null && _registry$select$get3 !== void 0 ? _registry$select$get3 : {}; // When the blockStyle is omitted, remove the block's preferred variation.

  if (!blockStyle) {
    const updatedVariations = { ...existingVariations
    };
    delete updatedVariations[blockName];
    registry.dispatch(external_gc_preferences_namespaceObject.store).set('core/edit-post', 'preferredStyleVariations', updatedVariations);
  } else {
    // Else add the variation.
    registry.dispatch(external_gc_preferences_namespaceObject.store).set('core/edit-post', 'preferredStyleVariations', { ...existingVariations,
      [blockName]: blockStyle
    });
  }
};
/**
 * Update the provided block types to be visible.
 *
 * @param {string[]} blockNames Names of block types to show.
 */

const showBlockTypes = blockNames => ({
  registry
}) => {
  var _registry$select$get4;

  const existingBlockNames = (_registry$select$get4 = registry.select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _registry$select$get4 !== void 0 ? _registry$select$get4 : [];
  const newBlockNames = existingBlockNames.filter(type => !(Array.isArray(blockNames) ? blockNames : [blockNames]).includes(type));
  registry.dispatch(external_gc_preferences_namespaceObject.store).set('core/edit-post', 'hiddenBlockTypes', newBlockNames);
};
/**
 * Update the provided block types to be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 */

const hideBlockTypes = blockNames => ({
  registry
}) => {
  var _registry$select$get5;

  const existingBlockNames = (_registry$select$get5 = registry.select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _registry$select$get5 !== void 0 ? _registry$select$get5 : [];
  const mergedBlockNames = new Set([...existingBlockNames, ...(Array.isArray(blockNames) ? blockNames : [blockNames])]);
  registry.dispatch(external_gc_preferences_namespaceObject.store).set('core/edit-post', 'hiddenBlockTypes', [...mergedBlockNames]);
};
/**
 * Stores info about which Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
 */

function setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
  return {
    type: 'SET_META_BOXES_PER_LOCATIONS',
    metaBoxesPerLocation
  };
}
/**
 * Update a metabox.
 */

const requestMetaBoxUpdates = () => async ({
  registry,
  select,
  dispatch
}) => {
  dispatch({
    type: 'REQUEST_META_BOX_UPDATES'
  }); // Saves the gc_editor fields.

  if (window.tinyMCE) {
    window.tinyMCE.triggerSave();
  } // Additional data needed for backward compatibility.
  // If we do not provide this data, the post will be overridden with the default values.


  const post = registry.select(external_gc_editor_namespaceObject.store).getCurrentPost();
  const additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, post.author ? ['post_author', post.author] : false].filter(Boolean); // We gather all the metaboxes locations data and the base form data.

  const baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
  const activeMetaBoxLocations = select.getActiveMetaBoxLocations();
  const formDataToMerge = [baseFormData, ...activeMetaBoxLocations.map(location => new window.FormData(getMetaBoxContainer(location)))]; // Merge all form data objects into a single one.

  const formData = formDataToMerge.reduce((memo, currentFormData) => {
    for (const [key, value] of currentFormData) {
      memo.append(key, value);
    }

    return memo;
  }, new window.FormData());
  additionalData.forEach(([key, value]) => formData.append(key, value));

  try {
    // Save the metaboxes.
    await external_gc_apiFetch_default()({
      url: window._gcMetaBoxUrl,
      method: 'POST',
      body: formData,
      parse: false
    });
    dispatch.metaBoxUpdatesSuccess();
  } catch {
    dispatch.metaBoxUpdatesFailure();
  }
};
/**
 * Returns an action object used to signal a successful meta box update.
 *
 * @return {Object} Action object.
 */

function metaBoxUpdatesSuccess() {
  return {
    type: 'META_BOX_UPDATES_SUCCESS'
  };
}
/**
 * Returns an action object used to signal a failed meta box update.
 *
 * @return {Object} Action object.
 */

function metaBoxUpdatesFailure() {
  return {
    type: 'META_BOX_UPDATES_FAILURE'
  };
}
/**
 * Returns an action object used to toggle the width of the editing canvas.
 *
 * @param {string} deviceType
 *
 * @return {Object} Action object.
 */

function __experimentalSetPreviewDeviceType(deviceType) {
  return {
    type: 'SET_PREVIEW_DEVICE_TYPE',
    deviceType
  };
}
/**
 * Returns an action object used to open/close the inserter.
 *
 * @param {boolean|Object} value                Whether the inserter should be
 *                                              opened (true) or closed (false).
 *                                              To specify an insertion point,
 *                                              use an object.
 * @param {string}         value.rootClientId   The root client ID to insert at.
 * @param {number}         value.insertionIndex The index to insert at.
 *
 * @return {Object} Action object.
 */

function setIsInserterOpened(value) {
  return {
    type: 'SET_IS_INSERTER_OPENED',
    value
  };
}
/**
 * Returns an action object used to open/close the list view.
 *
 * @param {boolean} isOpen A boolean representing whether the list view should be opened or closed.
 * @return {Object} Action object.
 */

function setIsListViewOpened(isOpen) {
  return {
    type: 'SET_IS_LIST_VIEW_OPENED',
    isOpen
  };
}
/**
 * Returns an action object used to switch to template editing.
 *
 * @param {boolean} value Is editing template.
 * @return {Object} Action object.
 */

function setIsEditingTemplate(value) {
  return {
    type: 'SET_IS_EDITING_TEMPLATE',
    value
  };
}
/**
 * Switches to the template mode.
 *
 * @param {boolean} newTemplate Is new template.
 */

const __unstableSwitchToTemplateMode = (newTemplate = false) => ({
  registry,
  select,
  dispatch
}) => {
  dispatch(setIsEditingTemplate(true));

  const message = newTemplate ? (0,external_gc_i18n_namespaceObject.__)("自定义模板已创建。你现在处于模板模式。") : (0,external_gc_i18n_namespaceObject.__)('编辑模板。此处所做的更改将影响使用该模板的所有文章和页面。');
  registry.dispatch(external_gc_notices_namespaceObject.store).createSuccessNotice(message, {
    type: 'snackbar'
  });

};
/**
 * Create a block based template.
 *
 * @param {Object?} template Template to create and assign.
 */

const __unstableCreateTemplate = template => async ({
  registry
}) => {
  const savedTemplate = await registry.dispatch(external_gc_coreData_namespaceObject.store).saveEntityRecord('postType', 'gc_template', template);
  const post = registry.select(external_gc_editor_namespaceObject.store).getCurrentPost();
  registry.dispatch(external_gc_coreData_namespaceObject.store).editEntityRecord('postType', post.type, post.id, {
    template: savedTemplate.slug
  });
};
let actions_metaBoxesInitialized = false;
/**
 * Initializes GeChiUI `postboxes` script and the logic for saving meta boxes.
 */

const initializeMetaBoxes = () => ({
  registry,
  select,
  dispatch
}) => {
  const isEditorReady = registry.select(external_gc_editor_namespaceObject.store).__unstableIsEditorReady();

  if (!isEditorReady) {
    return;
  } // Only initialize once.


  if (actions_metaBoxesInitialized) {
    return;
  }

  const postType = registry.select(external_gc_editor_namespaceObject.store).getCurrentPostType();

  if (window.postboxes.page !== postType) {
    window.postboxes.add_postbox_toggles(postType);
  }

  actions_metaBoxesInitialized = true;
  let wasSavingPost = registry.select(external_gc_editor_namespaceObject.store).isSavingPost();
  let wasAutosavingPost = registry.select(external_gc_editor_namespaceObject.store).isAutosavingPost(); // Save metaboxes when performing a full save on the post.

  registry.subscribe(async () => {
    const isSavingPost = registry.select(external_gc_editor_namespaceObject.store).isSavingPost();
    const isAutosavingPost = registry.select(external_gc_editor_namespaceObject.store).isAutosavingPost(); // Save metaboxes on save completion, except for autosaves.

    const shouldTriggerMetaboxesSave = wasSavingPost && !wasAutosavingPost && !isSavingPost && select.hasMetaBoxes(); // Save current state for next inspection.

    wasSavingPost = isSavingPost;
    wasAutosavingPost = isAutosavingPost;

    if (shouldTriggerMetaboxesSave) {
      await dispatch.requestMetaBoxUpdates();
    }
  });
  dispatch({
    type: 'META_BOXES_INITIALIZED'
  });
};

;// CONCATENATED MODULE: ./node_modules/rememo/rememo.js


/** @typedef {(...args: any[]) => *[]} GetDependants */

/** @typedef {() => void} Clear */

/**
 * @typedef {{
 *   getDependants: GetDependants,
 *   clear: Clear
 * }} EnhancedSelector
 */

/**
 * Internal cache entry.
 *
 * @typedef CacheNode
 *
 * @property {?CacheNode|undefined} [prev] Previous node.
 * @property {?CacheNode|undefined} [next] Next node.
 * @property {*[]} args Function arguments for cache entry.
 * @property {*} val Function result.
 */

/**
 * @typedef Cache
 *
 * @property {Clear} clear Function to clear cache.
 * @property {boolean} [isUniqueByDependants] Whether dependants are valid in
 * considering cache uniqueness. A cache is unique if dependents are all arrays
 * or objects.
 * @property {CacheNode?} [head] Cache head.
 * @property {*[]} [lastDependants] Dependants from previous invocation.
 */

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {{}}
 */
var LEAF_KEY = {};

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
 * @template {(...args: *[]) => *} S
 *
 * @param {S} selector Selector function.
 * @param {GetDependants=} getDependants Dependant getter returning an array of
 * references used in cache bust consideration.
 */
/* harmony default export */ function rememo(selector, getDependants) {
	/** @type {WeakMap<*,*>} */
	var rootCache;

	/** @type {GetDependants} */
	var normalizedGetDependants = getDependants ? getDependants : arrayOf;

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
	 * @param {*[]} dependants Selector dependants.
	 *
	 * @return {Cache} Cache object.
	 */
	function getCache(dependants) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i,
			dependant,
			map,
			cache;

		for (i = 0; i < dependants.length; i++) {
			dependant = dependants[i];

			// Can only compose WeakMap from object-like key.
			if (!isObjectLike(dependant)) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {*}    source    Source object for derivation.
	 * @param {...*} extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	/* eslint-enable jsdoc/check-param-names */
	function callSelector(/* source, ...extraArgs */) {
		var len = arguments.length,
			cache,
			node,
			i,
			args,
			dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		dependants = normalizedGetDependants.apply(null, args);
		cache = getCache(dependants);

		// If not guaranteed uniqueness by dependants (primitive type), shallow
		// compare against last dependants and, if references have changed,
		// destroy cache to recalculate result.
		if (!cache.isUniqueByDependants) {
			if (
				cache.lastDependants &&
				!isShallowEqual(dependants, cache.lastDependants, 0)
			) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				/** @type {CacheNode} */ (cache.head).prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (cache.head) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */







const EMPTY_ARRAY = [];
const EMPTY_OBJECT = {};
/**
 * Returns the current editing mode.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Editing mode.
 */

const getEditorMode = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => () => {
  var _select$get;

  return (_select$get = select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'editorMode')) !== null && _select$get !== void 0 ? _select$get : 'visual';
});
/**
 * Returns true if the editor sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the editor sidebar is opened.
 */

const isEditorSidebarOpened = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => () => {
  const activeGeneralSidebar = select(store).getActiveComplementaryArea('core/edit-post');
  return ['edit-post/document', 'edit-post/block'].includes(activeGeneralSidebar);
});
/**
 * Returns true if the plugin sidebar is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the plugin sidebar is opened.
 */

const isPluginSidebarOpened = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => () => {
  const activeGeneralSidebar = select(store).getActiveComplementaryArea('core/edit-post');
  return !!activeGeneralSidebar && !['edit-post/document', 'edit-post/block'].includes(activeGeneralSidebar);
});
/**
 * Returns the current active general sidebar name, or null if there is no
 * general sidebar active. The active general sidebar is a unique name to
 * identify either an editor or plugin sidebar.
 *
 * Examples:
 *
 *  - `edit-post/document`
 *  - `my-plugin/insert-image-sidebar`
 *
 * @param {Object} state Global application state.
 *
 * @return {?string} Active general sidebar name.
 */

const getActiveGeneralSidebarName = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => () => {
  return select(store).getActiveComplementaryArea('core/edit-post');
});
/**
 * Converts panels from the new preferences store format to the old format
 * that the post editor previously used.
 *
 * The resultant converted data should look like this:
 * {
 *     panelName: {
 *         enabled: false,
 *         opened: true,
 *     },
 *     anotherPanelName: {
 *         opened: true
 *     },
 * }
 *
 * @param {string[] | undefined} inactivePanels An array of inactive panel names.
 * @param {string[] | undefined} openPanels     An array of open panel names.
 *
 * @return {Object} The converted panel data.
 */

function convertPanelsToOldFormat(inactivePanels, openPanels) {
  var _ref;

  // First reduce the inactive panels.
  const panelsWithEnabledState = inactivePanels?.reduce((accumulatedPanels, panelName) => ({ ...accumulatedPanels,
    [panelName]: {
      enabled: false
    }
  }), {}); // Then reduce the open panels, passing in the result of the previous
  // reduction as the initial value so that both open and inactive
  // panel state is combined.

  const panels = openPanels?.reduce((accumulatedPanels, panelName) => {
    const currentPanelState = accumulatedPanels?.[panelName];
    return { ...accumulatedPanels,
      [panelName]: { ...currentPanelState,
        opened: true
      }
    };
  }, panelsWithEnabledState !== null && panelsWithEnabledState !== void 0 ? panelsWithEnabledState : {}); // The panels variable will only be set if openPanels wasn't `undefined`.
  // If it isn't set just return `panelsWithEnabledState`, and if that isn't
  // set return an empty object.

  return (_ref = panels !== null && panels !== void 0 ? panels : panelsWithEnabledState) !== null && _ref !== void 0 ? _ref : EMPTY_OBJECT;
}
/**
 * Returns the preferences (these preferences are persisted locally).
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Preferences Object.
 */


const getPreferences = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => () => {
  external_gc_deprecated_default()(`select( 'core/edit-post' ).getPreferences`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get`
  }); // These preferences now exist in the preferences store.
  // Fetch them so that they can be merged into the post
  // editor preferences.

  const preferences = ['hiddenBlockTypes', 'editorMode', 'preferredStyleVariations'].reduce((accumulatedPrefs, preferenceKey) => {
    const value = select(external_gc_preferences_namespaceObject.store).get('core/edit-post', preferenceKey);
    return { ...accumulatedPrefs,
      [preferenceKey]: value
    };
  }, {}); // Panels were a preference, but the data structure changed when the state
  // was migrated to the preferences store. They need to be converted from
  // the new preferences store format to old format to ensure no breaking
  // changes for plugins.

  const inactivePanels = select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'inactivePanels');
  const openPanels = select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'openPanels');
  const panels = convertPanelsToOldFormat(inactivePanels, openPanels);
  return { ...preferences,
    panels
  };
});
/**
 *
 * @param {Object} state         Global application state.
 * @param {string} preferenceKey Preference Key.
 * @param {*}      defaultValue  Default Value.
 *
 * @return {*} Preference Value.
 */

function getPreference(state, preferenceKey, defaultValue) {
  external_gc_deprecated_default()(`select( 'core/edit-post' ).getPreference`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get`
  }); // Avoid using the `getPreferences` registry selector where possible.

  const preferences = getPreferences(state);
  const value = preferences[preferenceKey];
  return value === undefined ? defaultValue : value;
}
/**
 * Returns an array of blocks that are hidden.
 *
 * @return {Array} A list of the hidden block types
 */

const getHiddenBlockTypes = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => () => {
  var _select$get2;

  return (_select$get2 = select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'hiddenBlockTypes')) !== null && _select$get2 !== void 0 ? _select$get2 : EMPTY_ARRAY;
});
/**
 * Returns true if the publish sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the publish sidebar is open.
 */

function isPublishSidebarOpened(state) {
  return state.publishSidebarActive;
}
/**
 * Returns true if the given panel was programmatically removed, or false otherwise.
 * All panels are not removed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is removed.
 */

function isEditorPanelRemoved(state, panelName) {
  return state.removedPanels.includes(panelName);
}
/**
 * Returns true if the given panel is enabled, or false otherwise. Panels are
 * enabled by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is enabled.
 */

const isEditorPanelEnabled = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  const inactivePanels = select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'inactivePanels');
  return !isEditorPanelRemoved(state, panelName) && !inactivePanels?.includes(panelName);
});
/**
 * Returns true if the given panel is open, or false otherwise. Panels are
 * closed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is open.
 */

const isEditorPanelOpened = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, panelName) => {
  const openPanels = select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'openPanels');
  return !!openPanels?.includes(panelName);
});
/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @deprecated since GC 6.3 use `core/interface` store's selector with the same name instead.
 *
 * @param {Object} state     Global application state.
 * @param {string} modalName A string that uniquely identifies the modal.
 *
 * @return {boolean} Whether the modal is active.
 */

const selectors_isModalActive = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, modalName) => {
  external_gc_deprecated_default()(`select( 'core/edit-post' ).isModalActive`, {
    since: '6.3',
    alternative: `select( 'core/interface' ).isModalActive`
  });
  return !!select(store).isModalActive(modalName);
});
/**
 * Returns whether the given feature is enabled or not.
 *
 * @param {Object} state   Global application state.
 * @param {string} feature Feature slug.
 *
 * @return {boolean} Is active.
 */

const selectors_isFeatureActive = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, feature) => {
  return !!select(external_gc_preferences_namespaceObject.store).get('core/edit-post', feature);
});
/**
 * Returns true if the plugin item is pinned to the header.
 * When the value is not set it defaults to true.
 *
 * @param {Object} state      Global application state.
 * @param {string} pluginName Plugin item name.
 *
 * @return {boolean} Whether the plugin item is pinned.
 */

const isPluginItemPinned = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => (state, pluginName) => {
  return select(store).isItemPinned('core/edit-post', pluginName);
});
/**
 * Returns an array of active meta box locations.
 *
 * @param {Object} state Post editor state.
 *
 * @return {string[]} Active meta box locations.
 */

const getActiveMetaBoxLocations = rememo(state => {
  return Object.keys(state.metaBoxes.locations).filter(location => isMetaBoxLocationActive(state, location));
}, state => [state.metaBoxes.locations]);
/**
 * Returns true if a metabox location is active and visible
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active and visible.
 */

function isMetaBoxLocationVisible(state, location) {
  return isMetaBoxLocationActive(state, location) && getMetaBoxesPerLocation(state, location)?.some(({
    id
  }) => {
    return isEditorPanelEnabled(state, `meta-box-${id}`);
  });
}
/**
 * Returns true if there is an active meta box in the given location, or false
 * otherwise.
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active.
 */

function isMetaBoxLocationActive(state, location) {
  const metaBoxes = getMetaBoxesPerLocation(state, location);
  return !!metaBoxes && metaBoxes.length !== 0;
}
/**
 * Returns the list of all the available meta boxes for a given location.
 *
 * @param {Object} state    Global application state.
 * @param {string} location Meta box location to test.
 *
 * @return {?Array} List of meta boxes.
 */

function getMetaBoxesPerLocation(state, location) {
  return state.metaBoxes.locations[location];
}
/**
 * Returns the list of all the available meta boxes.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} List of meta boxes.
 */

const getAllMetaBoxes = rememo(state => {
  return Object.values(state.metaBoxes.locations).flat();
}, state => [state.metaBoxes.locations]);
/**
 * Returns true if the post is using Meta Boxes
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether there are metaboxes or not.
 */

function hasMetaBoxes(state) {
  return getActiveMetaBoxLocations(state).length > 0;
}
/**
 * Returns true if the Meta Boxes are being saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the metaboxes are being saved.
 */

function selectors_isSavingMetaBoxes(state) {
  return state.metaBoxes.isSaving;
}
/**
 * Returns the current editing canvas device type.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Device type.
 */

function __experimentalGetPreviewDeviceType(state) {
  return state.deviceType;
}
/**
 * Returns true if the inserter is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the inserter is opened.
 */

function isInserterOpened(state) {
  return !!state.blockInserterPanel;
}
/**
 * Get the insertion point for the inserter.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID, index to insert at and starting filter value.
 */

function __experimentalGetInsertionPoint(state) {
  const {
    rootClientId,
    insertionIndex,
    filterValue
  } = state.blockInserterPanel;
  return {
    rootClientId,
    insertionIndex,
    filterValue
  };
}
/**
 * Returns true if the list view is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the list view is opened.
 */

function isListViewOpened(state) {
  return state.listViewPanel;
}
/**
 * Returns true if the template editing mode is enabled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether we're editing the template.
 */

function selectors_isEditingTemplate(state) {
  return state.isEditingTemplate;
}
/**
 * Returns true if meta boxes are initialized.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether meta boxes are initialized.
 */

function areMetaBoxesInitialized(state) {
  return state.metaBoxes.initialized;
}
/**
 * Retrieves the template of the currently edited post.
 *
 * @return {Object?} Post Template.
 */

const getEditedPostTemplate = (0,external_gc_data_namespaceObject.createRegistrySelector)(select => () => {
  const currentTemplate = select(external_gc_editor_namespaceObject.store).getEditedPostAttribute('template');

  if (currentTemplate) {
    const templateWithSameSlug = select(external_gc_coreData_namespaceObject.store).getEntityRecords('postType', 'gc_template', {
      per_page: -1
    })?.find(template => template.slug === currentTemplate);

    if (!templateWithSameSlug) {
      return templateWithSameSlug;
    }

    return select(external_gc_coreData_namespaceObject.store).getEditedEntityRecord('postType', 'gc_template', templateWithSameSlug.id);
  }

  const post = select(external_gc_editor_namespaceObject.store).getCurrentPost();

  if (post.link) {
    return select(external_gc_coreData_namespaceObject.store).__experimentalGetTemplateForLink(post.link);
  }

  return null;
});

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const constants_STORE_NAME = 'core/edit-post';
/**
 * CSS selector string for the admin bar view post link anchor tag.
 *
 * @type {string}
 */

const VIEW_AS_LINK_SELECTOR = '#gc-admin-bar-view a';
/**
 * CSS selector string for the admin bar preview post link anchor tag.
 *
 * @type {string}
 */

const VIEW_AS_PREVIEW_LINK_SELECTOR = '#gc-admin-bar-preview a';

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/store/index.js
/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Store definition for the edit post namespace.
 *
 * @see https://github.com/GeChiUI/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store_store = (0,external_gc_data_namespaceObject.createReduxStore)(constants_STORE_NAME, {
  reducer: store_reducer,
  actions: store_actions_namespaceObject,
  selectors: store_selectors_namespaceObject
});
(0,external_gc_data_namespaceObject.register)(store_store);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/text-editor/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


function TextEditor() {
  const isRichEditingEnabled = (0,external_gc_data_namespaceObject.useSelect)(select => {
    return select(external_gc_editor_namespaceObject.store).getEditorSettings().richEditingEnabled;
  }, []);
  const {
    switchEditorMode
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-text-editor"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.TextEditorGlobalKeyboardShortcuts, null), isRichEditingEnabled && (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-text-editor__toolbar"
  }, (0,external_gc_element_namespaceObject.createElement)("h2", null, (0,external_gc_i18n_namespaceObject.__)('正在编辑代码')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: () => switchEditorMode('visual'),
    shortcut: external_gc_keycodes_namespaceObject.displayShortcut.secondary('m')
  }, (0,external_gc_i18n_namespaceObject.__)('退出代码编辑器'))), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-text-editor__body"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTitle, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTextEditor, null)));
}

;// CONCATENATED MODULE: external ["gc","privateApis"]
var external_gc_privateApis_namespaceObject = window["gc"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/lock-unlock.js
/**
 * GeChiUI dependencies
 */

const {
  lock,
  unlock
} = (0,external_gc_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next GeChiUI release.', '@gechiui/edit-post');

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/visual-editor/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */









/**
 * Internal dependencies
 */



const {
  LayoutStyle,
  useLayoutClasses,
  useLayoutStyles
} = unlock(external_gc_blockEditor_namespaceObject.privateApis);
const isGutenbergPlugin =  false ? 0 : false;

function MaybeIframe({
  children,
  contentRef,
  shouldIframe,
  styles,
  style
}) {
  const ref = (0,external_gc_blockEditor_namespaceObject.__unstableUseMouseMoveTypingReset)();

  if (!shouldIframe) {
    return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__unstableEditorStyles, {
      styles: styles
    }), (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.WritingFlow, {
      ref: contentRef,
      className: "editor-styles-wrapper",
      style: {
        flex: '1',
        ...style
      },
      tabIndex: -1
    }, children));
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__unstableIframe, {
    ref: ref,
    contentRef: contentRef,
    style: {
      width: '100%',
      height: '100%',
      display: 'block'
    },
    name: "editor-canvas"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__unstableEditorStyles, {
    styles: styles
  }), children);
}
/**
 * Given an array of nested blocks, find the first Post Content
 * block inside it, recursing through any nesting levels,
 * and return its attributes.
 *
 * @param {Array} blocks A list of blocks.
 *
 * @return {Object | undefined} The Post Content block.
 */


function getPostContentAttributes(blocks) {
  for (let i = 0; i < blocks.length; i++) {
    if (blocks[i].name === 'core/post-content') {
      return blocks[i].attributes;
    }

    if (blocks[i].innerBlocks.length) {
      const nestedPostContent = getPostContentAttributes(blocks[i].innerBlocks);

      if (nestedPostContent) {
        return nestedPostContent;
      }
    }
  }
}

function VisualEditor({
  styles
}) {
  const {
    deviceType,
    isTemplateMode,
    postContentAttributes,
    editedPostTemplate = {},
    wrapperBlockName,
    wrapperUniqueId,
    isBlockBasedTheme,
    hasV3BlocksOnly
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      isFeatureActive,
      isEditingTemplate,
      getEditedPostTemplate,
      __experimentalGetPreviewDeviceType
    } = select(store_store);
    const {
      getCurrentPostId,
      getCurrentPostType,
      getEditorSettings
    } = select(external_gc_editor_namespaceObject.store);
    const {
      getBlockTypes
    } = select(external_gc_blocks_namespaceObject.store);

    const _isTemplateMode = isEditingTemplate();

    let _wrapperBlockName;

    if (getCurrentPostType() === 'gc_block') {
      _wrapperBlockName = 'core/block';
    } else if (!_isTemplateMode) {
      _wrapperBlockName = 'core/post-content';
    }

    const editorSettings = getEditorSettings();
    const supportsTemplateMode = editorSettings.supportsTemplateMode;
    const canEditTemplate = select(external_gc_coreData_namespaceObject.store).canUser('create', 'templates');
    return {
      deviceType: __experimentalGetPreviewDeviceType(),
      isTemplateMode: _isTemplateMode,
      postContentAttributes: getEditorSettings().postContentAttributes,
      // Post template fetch returns a 404 on classic themes, which
      // messes with e2e tests, so check it's a block theme first.
      editedPostTemplate: supportsTemplateMode && canEditTemplate ? getEditedPostTemplate() : undefined,
      wrapperBlockName: _wrapperBlockName,
      wrapperUniqueId: getCurrentPostId(),
      isBlockBasedTheme: editorSettings.__unstableIsBlockBasedTheme,
      hasV3BlocksOnly: getBlockTypes().every(type => {
        return type.apiVersion >= 3;
      })
    };
  }, []);
  const {
    isCleanNewPost
  } = (0,external_gc_data_namespaceObject.useSelect)(external_gc_editor_namespaceObject.store);
  const hasMetaBoxes = (0,external_gc_data_namespaceObject.useSelect)(select => select(store_store).hasMetaBoxes(), []);
  const {
    hasRootPaddingAwareAlignments,
    isFocusMode,
    themeHasDisabledLayoutStyles,
    themeSupportsLayout
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const _settings = select(external_gc_blockEditor_namespaceObject.store).getSettings();

    return {
      themeHasDisabledLayoutStyles: _settings.disableLayoutStyles,
      themeSupportsLayout: _settings.supportsLayout,
      isFocusMode: _settings.focusMode,
      hasRootPaddingAwareAlignments: _settings.__experimentalFeatures?.useRootPaddingAwareAlignments
    };
  }, []);
  const desktopCanvasStyles = {
    height: '100%',
    width: '100%',
    margin: 0,
    display: 'flex',
    flexFlow: 'column',
    // Default background color so that grey
    // .edit-post-editor-regions__content color doesn't show through.
    background: 'white'
  };
  const templateModeStyles = { ...desktopCanvasStyles,
    borderRadius: '2px 2px 0 0',
    border: '1px solid #ddd',
    borderBottom: 0
  };
  const resizedCanvasStyles = (0,external_gc_blockEditor_namespaceObject.__experimentalUseResizeCanvas)(deviceType, isTemplateMode);
  const globalLayoutSettings = (0,external_gc_blockEditor_namespaceObject.useSetting)('layout');
  const previewMode = 'is-' + deviceType.toLowerCase() + '-preview';
  let animatedStyles = isTemplateMode ? templateModeStyles : desktopCanvasStyles;

  if (resizedCanvasStyles) {
    animatedStyles = resizedCanvasStyles;
  }

  let paddingBottom; // Add a constant padding for the typewritter effect. When typing at the
  // bottom, there needs to be room to scroll up.

  if (!hasMetaBoxes && !resizedCanvasStyles && !isTemplateMode) {
    paddingBottom = '40vh';
  }

  const ref = (0,external_gc_element_namespaceObject.useRef)();
  const contentRef = (0,external_gc_compose_namespaceObject.useMergeRefs)([ref, (0,external_gc_blockEditor_namespaceObject.__unstableUseClipboardHandler)(), (0,external_gc_blockEditor_namespaceObject.__unstableUseTypewriter)(), (0,external_gc_blockEditor_namespaceObject.__unstableUseTypingObserver)(), (0,external_gc_blockEditor_namespaceObject.__unstableUseBlockSelectionClearer)()]);
  const blockSelectionClearerRef = (0,external_gc_blockEditor_namespaceObject.__unstableUseBlockSelectionClearer)(); // fallbackLayout is used if there is no Post Content,
  // and for Post Title.

  const fallbackLayout = (0,external_gc_element_namespaceObject.useMemo)(() => {
    if (isTemplateMode) {
      return {
        type: 'default'
      };
    }

    if (themeSupportsLayout) {
      // We need to ensure support for wide and full alignments,
      // so we add the constrained type.
      return { ...globalLayoutSettings,
        type: 'constrained'
      };
    } // Set default layout for classic themes so all alignments are supported.


    return {
      type: 'default'
    };
  }, [isTemplateMode, themeSupportsLayout, globalLayoutSettings]);
  const newestPostContentAttributes = (0,external_gc_element_namespaceObject.useMemo)(() => {
    if (!editedPostTemplate?.content && !editedPostTemplate?.blocks) {
      return postContentAttributes;
    } // When in template editing mode, we can access the blocks directly.


    if (editedPostTemplate?.blocks) {
      return getPostContentAttributes(editedPostTemplate?.blocks);
    } // If there are no blocks, we have to parse the content string.
    // Best double-check it's a string otherwise the parse function gets unhappy.


    const parseableContent = typeof editedPostTemplate?.content === 'string' ? editedPostTemplate?.content : '';
    return getPostContentAttributes((0,external_gc_blocks_namespaceObject.parse)(parseableContent)) || {};
  }, [editedPostTemplate?.content, editedPostTemplate?.blocks, postContentAttributes]);
  const {
    layout = {},
    align = ''
  } = newestPostContentAttributes || {};
  const postContentLayoutClasses = useLayoutClasses(newestPostContentAttributes, 'core/post-content');
  const blockListLayoutClass = classnames_default()({
    'is-layout-flow': !themeSupportsLayout
  }, themeSupportsLayout && postContentLayoutClasses, align && `align${align}`);
  const postContentLayoutStyles = useLayoutStyles(newestPostContentAttributes, 'core/post-content', '.block-editor-block-list__layout.is-root-container'); // Update type for blocks using legacy layouts.

  const postContentLayout = (0,external_gc_element_namespaceObject.useMemo)(() => {
    return layout && (layout?.type === 'constrained' || layout?.inherit || layout?.contentSize || layout?.wideSize) ? { ...globalLayoutSettings,
      ...layout,
      type: 'constrained'
    } : { ...globalLayoutSettings,
      ...layout,
      type: 'default'
    };
  }, [layout?.type, layout?.inherit, layout?.contentSize, layout?.wideSize, globalLayoutSettings]); // If there is a Post Content block we use its layout for the block list;
  // if not, this must be a classic theme, in which case we use the fallback layout.

  const blockListLayout = postContentAttributes ? postContentLayout : fallbackLayout;
  const titleRef = (0,external_gc_element_namespaceObject.useRef)();
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (!isCleanNewPost()) {
      return;
    }

    titleRef?.current?.focus();
  }, [isCleanNewPost]);
  styles = (0,external_gc_element_namespaceObject.useMemo)(() => [...styles, {
    // We should move this in to future to the body.
    css: `.edit-post-visual-editor__post-title-wrapper{margin-top:4rem}` + (paddingBottom ? `body{padding-bottom:${paddingBottom}}` : '')
  }], [styles]); // Add some styles for alignwide/alignfull Post Content and its children.

  const alignCSS = `.is-root-container.alignwide { max-width: var(--gc--style--global--wide-size); margin-left: auto; margin-right: auto;}
		.is-root-container.alignwide:where(.is-layout-flow) > :not(.alignleft):not(.alignright) { max-width: var(--gc--style--global--wide-size);}
		.is-root-container.alignfull { max-width: none; margin-left: auto; margin-right: auto;}
		.is-root-container.alignfull:where(.is-layout-flow) > :not(.alignleft):not(.alignright) { max-width: none;}`;
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockTools, {
    __unstableContentRef: ref,
    className: classnames_default()('edit-post-visual-editor', {
      'is-template-mode': isTemplateMode
    })
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.VisualEditorGlobalKeyboardShortcuts, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__unstableMotion.div, {
    className: "edit-post-visual-editor__content-area",
    animate: {
      padding: isTemplateMode ? '48px 48px 0' : 0
    },
    ref: blockSelectionClearerRef
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__unstableMotion.div, {
    animate: animatedStyles,
    initial: desktopCanvasStyles,
    className: previewMode
  }, (0,external_gc_element_namespaceObject.createElement)(MaybeIframe, {
    shouldIframe: (hasV3BlocksOnly || isGutenbergPlugin && isBlockBasedTheme) && !hasMetaBoxes || isTemplateMode || deviceType === 'Tablet' || deviceType === 'Mobile',
    contentRef: contentRef,
    styles: styles
  }, themeSupportsLayout && !themeHasDisabledLayoutStyles && !isTemplateMode && (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(LayoutStyle, {
    selector: ".edit-post-visual-editor__post-title-wrapper",
    layout: fallbackLayout
  }), (0,external_gc_element_namespaceObject.createElement)(LayoutStyle, {
    selector: ".block-editor-block-list__layout.is-root-container",
    layout: blockListLayout
  }), align && (0,external_gc_element_namespaceObject.createElement)(LayoutStyle, {
    css: alignCSS
  }), postContentLayoutStyles && (0,external_gc_element_namespaceObject.createElement)(LayoutStyle, {
    layout: postContentLayout,
    css: postContentLayoutStyles
  })), !isTemplateMode && (0,external_gc_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-post-visual-editor__post-title-wrapper', {
      'is-focus-mode': isFocusMode,
      'has-global-padding': hasRootPaddingAwareAlignments
    }),
    contentEditable: false
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTitle, {
    ref: titleRef
  })), (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__experimentalRecursionProvider, {
    blockName: wrapperBlockName,
    uniqueId: wrapperUniqueId
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockList, {
    className: isTemplateMode ? 'gc-site-blocks' : `${blockListLayoutClass} gc-block-post-content` // Ensure root level blocks receive default/flow blockGap styling rules.
    ,
    layout: blockListLayout
  }))))));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/keyboard-shortcuts/index.js
/**
 * GeChiUI dependencies
 */









/**
 * Internal dependencies
 */



function KeyboardShortcuts() {
  const {
    getBlockSelectionStart
  } = (0,external_gc_data_namespaceObject.useSelect)(external_gc_blockEditor_namespaceObject.store);
  const {
    getEditorMode,
    isEditorSidebarOpened,
    isListViewOpened,
    isFeatureActive
  } = (0,external_gc_data_namespaceObject.useSelect)(store_store);
  const isModeToggleDisabled = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      richEditingEnabled,
      codeEditingEnabled
    } = select(external_gc_editor_namespaceObject.store).getEditorSettings();
    return !richEditingEnabled || !codeEditingEnabled;
  }, []);
  const {
    createInfoNotice
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_notices_namespaceObject.store);
  const {
    switchEditorMode,
    openGeneralSidebar,
    closeGeneralSidebar,
    toggleFeature,
    setIsListViewOpened,
    setIsInserterOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    registerShortcut
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_keyboardShortcuts_namespaceObject.store);
  const {
    set: setPreference
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_preferences_namespaceObject.store);

  const toggleDistractionFree = () => {
    setPreference('core/edit-post', 'fixedToolbar', false);
    setIsInserterOpened(false);
    setIsListViewOpened(false);
    closeGeneralSidebar();
  };

  const {
    replaceBlocks
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_blockEditor_namespaceObject.store);
  const {
    getBlockName,
    getSelectedBlockClientId,
    getBlockAttributes
  } = (0,external_gc_data_namespaceObject.useSelect)(external_gc_blockEditor_namespaceObject.store);

  const handleTextLevelShortcut = (event, level) => {
    event.preventDefault();
    const destinationBlockName = level === 0 ? 'core/paragraph' : 'core/heading';
    const currentClientId = getSelectedBlockClientId();

    if (currentClientId === null) {
      return;
    }

    const blockName = getBlockName(currentClientId);

    if (blockName !== 'core/paragraph' && blockName !== 'core/heading') {
      return;
    }

    const attributes = getBlockAttributes(currentClientId);
    const textAlign = blockName === 'core/paragraph' ? 'align' : 'textAlign';
    const destinationTextAlign = destinationBlockName === 'core/paragraph' ? 'align' : 'textAlign';
    replaceBlocks(currentClientId, (0,external_gc_blocks_namespaceObject.createBlock)(destinationBlockName, {
      level,
      content: attributes.content,
      ...{
        [destinationTextAlign]: attributes[textAlign]
      }
    }));
  };

  (0,external_gc_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/edit-post/toggle-mode',
      category: 'global',
      description: (0,external_gc_i18n_namespaceObject.__)('在可视化编辑器和代码编辑器间切换。'),
      keyCombination: {
        modifier: 'secondary',
        character: 'm'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-distraction-free',
      category: 'global',
      description: (0,external_gc_i18n_namespaceObject.__)('切换专注模式。'),
      keyCombination: {
        modifier: 'primaryShift',
        character: '\\'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-fullscreen',
      category: 'global',
      description: (0,external_gc_i18n_namespaceObject.__)('切换全屏模式'),
      keyCombination: {
        modifier: 'secondary',
        character: 'f'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-list-view',
      category: 'global',
      description: (0,external_gc_i18n_namespaceObject.__)('打开区块列表视图。'),
      keyCombination: {
        modifier: 'access',
        character: 'o'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-sidebar',
      category: 'global',
      description: (0,external_gc_i18n_namespaceObject.__)('显示或隐藏设置边栏。'),
      keyCombination: {
        modifier: 'primaryShift',
        character: ','
      }
    });
    registerShortcut({
      name: 'core/edit-post/next-region',
      category: 'global',
      description: (0,external_gc_i18n_namespaceObject.__)('导航至编辑器的下一个功能区域。'),
      keyCombination: {
        modifier: 'ctrl',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'n'
      }]
    });
    registerShortcut({
      name: 'core/edit-post/previous-region',
      category: 'global',
      description: (0,external_gc_i18n_namespaceObject.__)('导航至编辑器的上一个功能区域。'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }, {
        modifier: 'ctrlShift',
        character: '~'
      }]
    });
    registerShortcut({
      name: 'core/edit-post/keyboard-shortcuts',
      category: 'main',
      description: (0,external_gc_i18n_namespaceObject.__)('显示这些键盘快捷键。'),
      keyCombination: {
        modifier: 'access',
        character: 'h'
      }
    });
    registerShortcut({
      name: 'core/edit-post/transform-heading-to-paragraph',
      category: 'block-library',
      description: (0,external_gc_i18n_namespaceObject.__)('将标题转换为段落。'),
      keyCombination: {
        modifier: 'access',
        character: `0`
      }
    });
    [1, 2, 3, 4, 5, 6].forEach(level => {
      registerShortcut({
        name: `core/edit-post/transform-paragraph-to-heading-${level}`,
        category: 'block-library',
        description: (0,external_gc_i18n_namespaceObject.__)('将段落转换为标题。'),
        keyCombination: {
          modifier: 'access',
          character: `${level}`
        }
      });
    });
  }, []);
  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-mode', () => {
    switchEditorMode(getEditorMode() === 'visual' ? 'text' : 'visual');
  }, {
    isDisabled: isModeToggleDisabled
  });
  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-fullscreen', () => {
    toggleFeature('fullscreenMode');
  });
  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-distraction-free', () => {
    toggleDistractionFree();
    toggleFeature('distractionFree');
    createInfoNotice(isFeatureActive('distractionFree') ? (0,external_gc_i18n_namespaceObject.__)('已打开专注模式。') : (0,external_gc_i18n_namespaceObject.__)('专注模式已关闭。'), {
      id: 'core/edit-post/distraction-free-mode/notice',
      type: 'snackbar'
    });
  });
  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-sidebar', event => {
    // This shortcut has no known clashes, but use preventDefault to prevent any
    // obscure shortcuts from triggering.
    event.preventDefault();

    if (isEditorSidebarOpened()) {
      closeGeneralSidebar();
    } else {
      const sidebarToOpen = getBlockSelectionStart() ? 'edit-post/block' : 'edit-post/document';
      openGeneralSidebar(sidebarToOpen);
    }
  }); // Only opens the list view. Other functionality for this shortcut happens in the rendered sidebar.

  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-list-view', () => {
    if (!isListViewOpened()) {
      setIsListViewOpened(true);
    }
  });
  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/transform-heading-to-paragraph', event => handleTextLevelShortcut(event, 0));
  [1, 2, 3, 4, 5, 6].forEach(level => {
    //the loop is based off on a constant therefore
    //the hook will execute the same way every time
    //eslint-disable-next-line react-hooks/rules-of-hooks
    (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)(`core/edit-post/transform-paragraph-to-heading-${level}`, event => handleTextLevelShortcut(event, level));
  });
  return null;
}

/* harmony default export */ var keyboard_shortcuts = (KeyboardShortcuts);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/options/enable-custom-fields.js


/**
 * GeChiUI dependencies
 */






function CustomFieldsConfirmation({
  willEnable
}) {
  const [isReloading, setIsReloading] = (0,external_gc_element_namespaceObject.useState)(false);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)("p", {
    className: "edit-post-preferences-modal__custom-fields-confirmation-message"
  }, (0,external_gc_i18n_namespaceObject.__)('此更改需要刷新本页。请在刷新前确保您已经保存了您的内容。')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "edit-post-preferences-modal__custom-fields-confirmation-button",
    variant: "secondary",
    isBusy: isReloading,
    disabled: isReloading,
    onClick: () => {
      setIsReloading(true);
      document.getElementById('toggle-custom-fields-form').submit();
    }
  }, willEnable ? (0,external_gc_i18n_namespaceObject.__)('显示并重新加载页面') : (0,external_gc_i18n_namespaceObject.__)('隐藏并重新加载页面')));
}
function EnableCustomFieldsOption({
  label,
  areCustomFieldsEnabled
}) {
  const [isChecked, setIsChecked] = (0,external_gc_element_namespaceObject.useState)(areCustomFieldsEnabled);
  return (0,external_gc_element_namespaceObject.createElement)(preferences_modal_base_option, {
    label: label,
    isChecked: isChecked,
    onChange: setIsChecked
  }, isChecked !== areCustomFieldsEnabled && (0,external_gc_element_namespaceObject.createElement)(CustomFieldsConfirmation, {
    willEnable: isChecked
  }));
}
/* harmony default export */ var enable_custom_fields = ((0,external_gc_data_namespaceObject.withSelect)(select => ({
  areCustomFieldsEnabled: !!select(external_gc_editor_namespaceObject.store).getEditorSettings().enableCustomFields
}))(EnableCustomFieldsOption));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/options/enable-panel.js
/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */


/* harmony default export */ var enable_panel = ((0,external_gc_compose_namespaceObject.compose)((0,external_gc_data_namespaceObject.withSelect)((select, {
  panelName
}) => {
  const {
    isEditorPanelEnabled,
    isEditorPanelRemoved
  } = select(store_store);
  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), (0,external_gc_compose_namespaceObject.ifCondition)(({
  isRemoved
}) => !isRemoved), (0,external_gc_data_namespaceObject.withDispatch)((dispatch, {
  panelName
}) => ({
  onChange: () => dispatch(store_store).toggleEditorPanelEnabled(panelName)
})))(preferences_modal_base_option));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/options/enable-plugin-document-setting-panel.js


/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */


const {
  Fill,
  Slot: enable_plugin_document_setting_panel_Slot
} = (0,external_gc_components_namespaceObject.createSlotFill)('EnablePluginDocumentSettingPanelOption');

const EnablePluginDocumentSettingPanelOption = ({
  label,
  panelName
}) => (0,external_gc_element_namespaceObject.createElement)(Fill, null, (0,external_gc_element_namespaceObject.createElement)(enable_panel, {
  label: label,
  panelName: panelName
}));

EnablePluginDocumentSettingPanelOption.Slot = enable_plugin_document_setting_panel_Slot;
/* harmony default export */ var enable_plugin_document_setting_panel = (EnablePluginDocumentSettingPanelOption);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/options/enable-publish-sidebar.js
/**
 * GeChiUI dependencies
 */





/* harmony default export */ var enable_publish_sidebar = ((0,external_gc_compose_namespaceObject.compose)((0,external_gc_data_namespaceObject.withSelect)(select => ({
  isChecked: select(external_gc_editor_namespaceObject.store).isPublishSidebarEnabled()
})), (0,external_gc_data_namespaceObject.withDispatch)(dispatch => {
  const {
    enablePublishSidebar,
    disablePublishSidebar
  } = dispatch(external_gc_editor_namespaceObject.store);
  return {
    onChange: isEnabled => isEnabled ? enablePublishSidebar() : disablePublishSidebar()
  };
}), // In < medium viewports we override this option and always show the publish sidebar.
// See the edit-post's header component for the specific logic.
(0,external_gc_viewport_namespaceObject.ifViewportMatches)('medium'))(preferences_modal_base_option));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/options/enable-feature.js
/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */


/* harmony default export */ var enable_feature = ((0,external_gc_compose_namespaceObject.compose)((0,external_gc_data_namespaceObject.withSelect)((select, {
  featureName
}) => {
  const {
    isFeatureActive
  } = select(store_store);
  return {
    isChecked: isFeatureActive(featureName)
  };
}), (0,external_gc_data_namespaceObject.withDispatch)((dispatch, {
  featureName,
  onToggle = () => {}
}) => ({
  onChange: () => {
    onToggle();
    dispatch(store_store).toggleFeature(featureName);
  }
})))(preferences_modal_base_option));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/options/index.js






;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/meta-boxes-section.js


/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */



function MetaBoxesSection({
  areCustomFieldsRegistered,
  metaBoxes,
  ...sectionProps
}) {
  // The 'Custom Fields' meta box is a special case that we handle separately.
  const thirdPartyMetaBoxes = metaBoxes.filter(({
    id
  }) => id !== 'postcustom');

  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(preferences_modal_section, { ...sectionProps
  }, areCustomFieldsRegistered && (0,external_gc_element_namespaceObject.createElement)(enable_custom_fields, {
    label: (0,external_gc_i18n_namespaceObject.__)('自定义字段')
  }), thirdPartyMetaBoxes.map(({
    id,
    title
  }) => (0,external_gc_element_namespaceObject.createElement)(enable_panel, {
    key: id,
    label: title,
    panelName: `meta-box-${id}`
  })));
}
/* harmony default export */ var meta_boxes_section = ((0,external_gc_data_namespaceObject.withSelect)(select => {
  const {
    getEditorSettings
  } = select(external_gc_editor_namespaceObject.store);
  const {
    getAllMetaBoxes
  } = select(store_store);
  return {
    // This setting should not live in the block editor's store.
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== undefined,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/block-manager/checklist.js


/**
 * GeChiUI dependencies
 */



function BlockTypesChecklist({
  blockTypes,
  value,
  onItemChange
}) {
  return (0,external_gc_element_namespaceObject.createElement)("ul", {
    className: "edit-post-block-manager__checklist"
  }, blockTypes.map(blockType => (0,external_gc_element_namespaceObject.createElement)("li", {
    key: blockType.name,
    className: "edit-post-block-manager__checklist-item"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    label: blockType.title,
    checked: value.includes(blockType.name),
    onChange: (...args) => onItemChange(blockType.name, ...args)
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockIcon, {
    icon: blockType.icon
  }))));
}

/* harmony default export */ var checklist = (BlockTypesChecklist);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/block-manager/category.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */




function BlockManagerCategory({
  title,
  blockTypes
}) {
  const instanceId = (0,external_gc_compose_namespaceObject.useInstanceId)(BlockManagerCategory);
  const {
    defaultAllowedBlockTypes,
    hiddenBlockTypes
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings
    } = select(external_gc_editor_namespaceObject.store);
    const {
      getHiddenBlockTypes
    } = select(store_store);
    return {
      defaultAllowedBlockTypes: getEditorSettings().defaultAllowedBlockTypes,
      hiddenBlockTypes: getHiddenBlockTypes()
    };
  }, []);
  const filteredBlockTypes = (0,external_gc_element_namespaceObject.useMemo)(() => {
    if (defaultAllowedBlockTypes === true) {
      return blockTypes;
    }

    return blockTypes.filter(({
      name
    }) => {
      return defaultAllowedBlockTypes?.includes(name);
    });
  }, [defaultAllowedBlockTypes, blockTypes]);
  const {
    showBlockTypes,
    hideBlockTypes
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const toggleVisible = (0,external_gc_element_namespaceObject.useCallback)((blockName, nextIsChecked) => {
    if (nextIsChecked) {
      showBlockTypes(blockName);
    } else {
      hideBlockTypes(blockName);
    }
  }, []);
  const toggleAllVisible = (0,external_gc_element_namespaceObject.useCallback)(nextIsChecked => {
    const blockNames = blockTypes.map(({
      name
    }) => name);

    if (nextIsChecked) {
      showBlockTypes(blockNames);
    } else {
      hideBlockTypes(blockNames);
    }
  }, [blockTypes]);

  if (!filteredBlockTypes.length) {
    return null;
  }

  const checkedBlockNames = filteredBlockTypes.map(({
    name
  }) => name).filter(type => !hiddenBlockTypes.includes(type));
  const titleId = 'edit-post-block-manager__category-title-' + instanceId;
  const isAllChecked = checkedBlockNames.length === filteredBlockTypes.length;
  const isIndeterminate = !isAllChecked && checkedBlockNames.length > 0;
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    role: "group",
    "aria-labelledby": titleId,
    className: "edit-post-block-manager__category"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.CheckboxControl, {
    __nextHasNoMarginBottom: true,
    checked: isAllChecked,
    onChange: toggleAllVisible,
    className: "edit-post-block-manager__category-title",
    indeterminate: isIndeterminate,
    label: (0,external_gc_element_namespaceObject.createElement)("span", {
      id: titleId
    }, title)
  }), (0,external_gc_element_namespaceObject.createElement)(checklist, {
    blockTypes: filteredBlockTypes,
    value: checkedBlockNames,
    onItemChange: toggleVisible
  }));
}

/* harmony default export */ var block_manager_category = (BlockManagerCategory);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/block-manager/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */




function BlockManager({
  blockTypes,
  categories,
  hasBlockSupport,
  isMatchingSearchTerm,
  numberOfHiddenBlocks,
  enableAllBlockTypes
}) {
  const debouncedSpeak = (0,external_gc_compose_namespaceObject.useDebounce)(external_gc_a11y_namespaceObject.speak, 500);
  const [search, setSearch] = (0,external_gc_element_namespaceObject.useState)(''); // Filtering occurs here (as opposed to `withSelect`) to avoid
  // wasted renders by consequence of `Array#filter` producing
  // a new value reference on each call.

  blockTypes = blockTypes.filter(blockType => hasBlockSupport(blockType, 'inserter', true) && (!search || isMatchingSearchTerm(blockType, search)) && (!blockType.parent || blockType.parent.includes('core/post-content'))); // Announce search results on change

  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (!search) {
      return;
    }

    const count = blockTypes.length;
    const resultsFoundMessage = (0,external_gc_i18n_namespaceObject.sprintf)(
    /* translators: %d: number of results. */
    (0,external_gc_i18n_namespaceObject._n)('找到%d个结果。', '%d results found.', count), count);
    debouncedSpeak(resultsFoundMessage);
  }, [blockTypes.length, search, debouncedSpeak]);
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-block-manager__content"
  }, !!numberOfHiddenBlocks && (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-block-manager__disabled-blocks-count"
  }, (0,external_gc_i18n_namespaceObject.sprintf)(
  /* translators: %d: number of blocks. */
  (0,external_gc_i18n_namespaceObject._n)('%d个区块已被隐藏。', '%d blocks are hidden.', numberOfHiddenBlocks), numberOfHiddenBlocks), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => enableAllBlockTypes(blockTypes)
  }, (0,external_gc_i18n_namespaceObject.__)('重置'))), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.SearchControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_gc_i18n_namespaceObject.__)('搜索区块'),
    placeholder: (0,external_gc_i18n_namespaceObject.__)('搜索区块'),
    value: search,
    onChange: nextSearch => setSearch(nextSearch),
    className: "edit-post-block-manager__search"
  }), (0,external_gc_element_namespaceObject.createElement)("div", {
    tabIndex: "0",
    role: "region",
    "aria-label": (0,external_gc_i18n_namespaceObject.__)('可用的区块类型'),
    className: "edit-post-block-manager__results"
  }, blockTypes.length === 0 && (0,external_gc_element_namespaceObject.createElement)("p", {
    className: "edit-post-block-manager__no-results"
  }, (0,external_gc_i18n_namespaceObject.__)('未找到区块。')), categories.map(category => (0,external_gc_element_namespaceObject.createElement)(block_manager_category, {
    key: category.slug,
    title: category.title,
    blockTypes: blockTypes.filter(blockType => blockType.category === category.slug)
  })), (0,external_gc_element_namespaceObject.createElement)(block_manager_category, {
    title: (0,external_gc_i18n_namespaceObject.__)('未分类'),
    blockTypes: blockTypes.filter(({
      category
    }) => !category)
  })));
}

/* harmony default export */ var block_manager = ((0,external_gc_compose_namespaceObject.compose)([(0,external_gc_data_namespaceObject.withSelect)(select => {
  const {
    getBlockTypes,
    getCategories,
    hasBlockSupport,
    isMatchingSearchTerm
  } = select(external_gc_blocks_namespaceObject.store);
  const {
    getHiddenBlockTypes
  } = select(store_store); // Some hidden blocks become unregistered
  // by removing for instance the plugin that registered them, yet
  // they're still remain as hidden by the user's action.
  // We consider "hidden", blocks which were hidden and
  // are still registered.

  const blockTypes = getBlockTypes();
  const hiddenBlockTypes = getHiddenBlockTypes().filter(hiddenBlock => {
    return blockTypes.some(registeredBlock => registeredBlock.name === hiddenBlock);
  });
  const numberOfHiddenBlocks = Array.isArray(hiddenBlockTypes) && hiddenBlockTypes.length;
  return {
    blockTypes,
    categories: getCategories(),
    hasBlockSupport,
    isMatchingSearchTerm,
    numberOfHiddenBlocks
  };
}), (0,external_gc_data_namespaceObject.withDispatch)(dispatch => {
  const {
    showBlockTypes
  } = dispatch(store_store);
  return {
    enableAllBlockTypes: blockTypes => {
      const blockNames = blockTypes.map(({
        name
      }) => name);
      showBlockTypes(blockNames);
    }
  };
})])(BlockManager));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/preferences-modal/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */





const PREFERENCES_MODAL_NAME = 'edit-post/preferences';
function EditPostPreferencesModal() {
  const isLargeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('medium');
  const {
    closeModal
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);
  const [isModalActive, showBlockBreadcrumbsOption] = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings
    } = select(external_gc_editor_namespaceObject.store);
    const {
      getEditorMode,
      isFeatureActive
    } = select(store_store);
    const modalActive = select(store).isModalActive(PREFERENCES_MODAL_NAME);
    const mode = getEditorMode();
    const isRichEditingEnabled = getEditorSettings().richEditingEnabled;
    const isDistractionFreeEnabled = isFeatureActive('distractionFree');
    return [modalActive, !isDistractionFreeEnabled && isLargeViewport && isRichEditingEnabled && mode === 'visual', isDistractionFreeEnabled];
  }, [isLargeViewport]);
  const {
    closeGeneralSidebar,
    setIsListViewOpened,
    setIsInserterOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    set: setPreference
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_preferences_namespaceObject.store);

  const toggleDistractionFree = () => {
    setPreference('core/edit-post', 'fixedToolbar', false);
    setIsInserterOpened(false);
    setIsListViewOpened(false);
    closeGeneralSidebar();
  };

  const sections = (0,external_gc_element_namespaceObject.useMemo)(() => [{
    name: 'general',
    tabLabel: (0,external_gc_i18n_namespaceObject.__)('常规'),
    content: (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, isLargeViewport && (0,external_gc_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_gc_i18n_namespaceObject.__)('发布'),
      description: (0,external_gc_i18n_namespaceObject.__)('更改与发布相关的选项。')
    }, (0,external_gc_element_namespaceObject.createElement)(enable_publish_sidebar, {
      help: (0,external_gc_i18n_namespaceObject.__)('预览设置，如可见性和标签。'),
      label: (0,external_gc_i18n_namespaceObject.__)('包含预发布清单')
    })), (0,external_gc_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_gc_i18n_namespaceObject.__)('外观'),
      description: (0,external_gc_i18n_namespaceObject.__)('自定义与区块编辑器界面和编辑流程相关的选项。')
    }, (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "distractionFree",
      onToggle: toggleDistractionFree,
      help: (0,external_gc_i18n_namespaceObject.__)('通过隐藏工具栏和其他元素来集中精力写作，减少视觉干扰。'),
      label: (0,external_gc_i18n_namespaceObject.__)('专注')
    }), (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "focusMode",
      help: (0,external_gc_i18n_namespaceObject.__)('高亮显示当前区块并淡化其他内容。'),
      label: (0,external_gc_i18n_namespaceObject.__)('探照灯模式')
    }), (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "showIconLabels",
      label: (0,external_gc_i18n_namespaceObject.__)('显示按钮文字标签'),
      help: (0,external_gc_i18n_namespaceObject.__)('在按钮上显示文字而非图标')
    }), (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "showListViewByDefault",
      help: (0,external_gc_i18n_namespaceObject.__)('默认情况下打开区块列表视图的侧边栏。'),
      label: (0,external_gc_i18n_namespaceObject.__)('始终打开列表视图')
    }), (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "themeStyles",
      help: (0,external_gc_i18n_namespaceObject.__)('使编辑器看起来像您的主题。'),
      label: (0,external_gc_i18n_namespaceObject.__)('使用主题样式')
    }), showBlockBreadcrumbsOption && (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "showBlockBreadcrumbs",
      help: (0,external_gc_i18n_namespaceObject.__)('在编辑器的底部显示区块的面包屑。'),
      label: (0,external_gc_i18n_namespaceObject.__)('显示区块面包屑')
    })))
  }, {
    name: 'blocks',
    tabLabel: (0,external_gc_i18n_namespaceObject.__)('区块'),
    content: (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_gc_i18n_namespaceObject.__)('区块交互'),
      description: (0,external_gc_i18n_namespaceObject.__)('自定义您与区块库和编辑画布中的区块的交互方式。')
    }, (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "mostUsedBlocks",
      help: (0,external_gc_i18n_namespaceObject.__)('将最常见的区块放入区块库中。'),
      label: (0,external_gc_i18n_namespaceObject.__)('显示使用最多的区块')
    }), (0,external_gc_element_namespaceObject.createElement)(enable_feature, {
      featureName: "keepCaretInsideBlock",
      help: (0,external_gc_i18n_namespaceObject.__)('阻止文字光标离开区块以辅助屏幕阅读器。'),
      label: (0,external_gc_i18n_namespaceObject.__)('在区块内包含文本光标')
    })), (0,external_gc_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_gc_i18n_namespaceObject.__)('可见区块'),
      description: (0,external_gc_i18n_namespaceObject.__)("禁用您不希望在插入器中显示的区块。您可以随时将其还原。")
    }, (0,external_gc_element_namespaceObject.createElement)(block_manager, null)))
  }, {
    name: 'panels',
    tabLabel: (0,external_gc_i18n_namespaceObject.__)('面板'),
    content: (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_gc_i18n_namespaceObject.__)('文档设置'),
      description: (0,external_gc_i18n_namespaceObject.__)('选择面板所显示的内容。')
    }, (0,external_gc_element_namespaceObject.createElement)(enable_plugin_document_setting_panel.Slot, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTaxonomies, {
      taxonomyWrapper: (content, taxonomy) => (0,external_gc_element_namespaceObject.createElement)(enable_panel, {
        label: taxonomy.labels.menu_name,
        panelName: `taxonomy-panel-${taxonomy.slug}`
      })
    }), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostFeaturedImageCheck, null, (0,external_gc_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_gc_i18n_namespaceObject.__)('特色图片'),
      panelName: "featured-image"
    })), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostExcerptCheck, null, (0,external_gc_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_gc_i18n_namespaceObject.__)('摘要'),
      panelName: "post-excerpt"
    })), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTypeSupportCheck, {
      supportKeys: ['comments', 'trackbacks']
    }, (0,external_gc_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_gc_i18n_namespaceObject.__)('讨论'),
      panelName: "discussion-panel"
    })), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PageAttributesCheck, null, (0,external_gc_element_namespaceObject.createElement)(enable_panel, {
      label: (0,external_gc_i18n_namespaceObject.__)('页面属性'),
      panelName: "page-attributes"
    }))), (0,external_gc_element_namespaceObject.createElement)(meta_boxes_section, {
      title: (0,external_gc_i18n_namespaceObject.__)('额外'),
      description: (0,external_gc_i18n_namespaceObject.__)('向编辑器添加额外的区域。')
    }))
  }], [isLargeViewport, showBlockBreadcrumbsOption]);

  if (!isModalActive) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(PreferencesModal, {
    closeModal: closeModal
  }, (0,external_gc_element_namespaceObject.createElement)(PreferencesModalTabs, {
    sections: sections
  }));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/browser-url/index.js
/**
 * GeChiUI dependencies
 */




/**
 * Returns the Post's Edit URL.
 *
 * @param {number} postId Post ID.
 *
 * @return {string} Post edit URL.
 */

function getPostEditURL(postId) {
  return (0,external_gc_url_namespaceObject.addQueryArgs)('post.php', {
    post: postId,
    action: 'edit'
  });
}
/**
 * Returns the Post's Trashed URL.
 *
 * @param {number} postId   Post ID.
 * @param {string} postType Post Type.
 *
 * @return {string} Post trashed URL.
 */

function getPostTrashedURL(postId, postType) {
  return (0,external_gc_url_namespaceObject.addQueryArgs)('edit.php', {
    trashed: 1,
    post_type: postType,
    ids: postId
  });
}
class BrowserURL extends external_gc_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      historyId: null
    };
  }

  componentDidUpdate(prevProps) {
    const {
      postId,
      postStatus,
      postType,
      isSavingPost
    } = this.props;
    const {
      historyId
    } = this.state; // Posts are still dirty while saving so wait for saving to finish
    // to avoid the unsaved changes warning when trashing posts.

    if (postStatus === 'trash' && !isSavingPost) {
      this.setTrashURL(postId, postType);
      return;
    }

    if ((postId !== prevProps.postId || postId !== historyId) && postStatus !== 'auto-draft' && postId) {
      this.setBrowserURL(postId);
    }
  }
  /**
   * Navigates the browser to the post trashed URL to show a notice about the trashed post.
   *
   * @param {number} postId   Post ID.
   * @param {string} postType Post Type.
   */


  setTrashURL(postId, postType) {
    window.location.href = getPostTrashedURL(postId, postType);
  }
  /**
   * Replaces the browser URL with a post editor link for the given post ID.
   *
   * Note it is important that, since this function may be called when the
   * editor first loads, the result generated `getPostEditURL` matches that
   * produced by the server. Otherwise, the URL will change unexpectedly.
   *
   * @param {number} postId Post ID for which to generate post editor URL.
   */


  setBrowserURL(postId) {
    window.history.replaceState({
      id: postId
    }, 'Post ' + postId, getPostEditURL(postId));
    this.setState(() => ({
      historyId: postId
    }));
  }

  render() {
    return null;
  }

}
/* harmony default export */ var browser_url = ((0,external_gc_data_namespaceObject.withSelect)(select => {
  const {
    getCurrentPost,
    isSavingPost
  } = select(external_gc_editor_namespaceObject.store);
  const post = getCurrentPost();
  let {
    id,
    status,
    type
  } = post;
  const isTemplate = ['gc_template', 'gc_template_part'].includes(type);

  if (isTemplate) {
    id = post.gc_id;
  }

  return {
    postId: id,
    postStatus: status,
    postType: type,
    isSavingPost: isSavingPost()
  };
})(BrowserURL));

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/gechiui.js


/**
 * GeChiUI dependencies
 */

const gechiui = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M14.033 1.195a2.387 2.387 0 0 1-.87 3.235l-6.98 4.04a.602.602 0 0 0-.3.522v6.342a.6.6 0 0 0 .3.521l5.524 3.199a.585.585 0 0 0 .586 0l5.527-3.199a.603.603 0 0 0 .299-.52V11.39l-4.969 2.838a2.326 2.326 0 0 1-3.19-.9 2.388 2.388 0 0 1 .89-3.23l7.108-4.062C20.123 4.8 22.8 6.384 22.8 8.901v6.914a4.524 4.524 0 0 1-2.245 3.919l-6.345 3.672a4.407 4.407 0 0 1-4.422 0l-6.344-3.672A4.524 4.524 0 0 1 1.2 15.816V8.51a4.524 4.524 0 0 1 2.245-3.918l7.393-4.28a2.326 2.326 0 0 1 3.195.883z"
}));
/* harmony default export */ var library_gechiui = (gechiui);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/fullscreen-mode-close/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */









/**
 * Internal dependencies
 */



function FullscreenModeClose({
  showTooltip,
  icon,
  href
}) {
  var _postType$labels$view;

  const {
    isActive,
    isRequestingSiteIcon,
    postType,
    siteIconUrl
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getCurrentPostType
    } = select(external_gc_editor_namespaceObject.store);
    const {
      isFeatureActive
    } = select(store_store);
    const {
      getEntityRecord,
      getPostType,
      isResolving
    } = select(external_gc_coreData_namespaceObject.store);
    const siteData = getEntityRecord('root', '__unstableBase', undefined) || {};
    return {
      isActive: isFeatureActive('fullscreenMode'),
      isRequestingSiteIcon: isResolving('getEntityRecord', ['root', '__unstableBase', undefined]),
      postType: getPostType(getCurrentPostType()),
      siteIconUrl: siteData.site_icon_url
    };
  }, []);
  const disableMotion = (0,external_gc_compose_namespaceObject.useReducedMotion)();

  if (!isActive || !postType) {
    return null;
  }

  let buttonIcon = (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Icon, {
    size: "36px",
    icon: library_gechiui
  });
  const effect = {
    expand: {
      scale: 1.25,
      transition: {
        type: 'tween',
        duration: '0.3'
      }
    }
  };

  if (siteIconUrl) {
    buttonIcon = (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__unstableMotion.img, {
      variants: !disableMotion && effect,
      alt: (0,external_gc_i18n_namespaceObject.__)('系统图标'),
      className: "edit-post-fullscreen-mode-close_site-icon",
      src: siteIconUrl
    });
  }

  if (isRequestingSiteIcon) {
    buttonIcon = null;
  } // Override default icon if custom icon is provided via props.


  if (icon) {
    buttonIcon = (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Icon, {
      size: "36px",
      icon: icon
    });
  }

  const classes = classnames_default()({
    'edit-post-fullscreen-mode-close': true,
    'has-icon': siteIconUrl
  });
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__unstableMotion.div, {
    whileHover: "expand"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: classes,
    href: href !== null && href !== void 0 ? href : (0,external_gc_url_namespaceObject.addQueryArgs)('edit.php', {
      post_type: postType.slug
    }),
    label: (_postType$labels$view = postType?.labels?.view_items) !== null && _postType$labels$view !== void 0 ? _postType$labels$view : (0,external_gc_i18n_namespaceObject.__)('返回'),
    showTooltip: showTooltip
  }, buttonIcon));
}

/* harmony default export */ var fullscreen_mode_close = (FullscreenModeClose);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/list-view.js


/**
 * GeChiUI dependencies
 */

const listView = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z"
}));
/* harmony default export */ var list_view = (listView);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/plus.js


/**
 * GeChiUI dependencies
 */

const plus = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ var library_plus = (plus);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/header-toolbar/index.js


/**
 * GeChiUI dependencies
 */










/**
 * Internal dependencies
 */



const {
  useShouldContextualToolbarShow
} = unlock(external_gc_blockEditor_namespaceObject.privateApis);

const preventDefault = event => {
  event.preventDefault();
};

function HeaderToolbar() {
  const inserterButton = (0,external_gc_element_namespaceObject.useRef)();
  const {
    setIsInserterOpened,
    setIsListViewOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    get: getPreference
  } = (0,external_gc_data_namespaceObject.useSelect)(external_gc_preferences_namespaceObject.store);
  const hasFixedToolbar = getPreference('core/edit-post', 'fixedToolbar');
  const {
    isInserterEnabled,
    isInserterOpened,
    isTextModeEnabled,
    showIconLabels,
    isListViewOpen,
    listViewShortcut
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      hasInserterItems,
      getBlockRootClientId,
      getBlockSelectionEnd
    } = select(external_gc_blockEditor_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_gc_editor_namespaceObject.store);
    const {
      getEditorMode,
      isFeatureActive,
      isListViewOpened
    } = select(store_store);
    const {
      getShortcutRepresentation
    } = select(external_gc_keyboardShortcuts_namespaceObject.store);
    return {
      // This setting (richEditingEnabled) should not live in the block editor's setting.
      isInserterEnabled: getEditorMode() === 'visual' && getEditorSettings().richEditingEnabled && hasInserterItems(getBlockRootClientId(getBlockSelectionEnd())),
      isInserterOpened: select(store_store).isInserterOpened(),
      isTextModeEnabled: getEditorMode() === 'text',
      showIconLabels: isFeatureActive('showIconLabels'),
      isListViewOpen: isListViewOpened(),
      listViewShortcut: getShortcutRepresentation('core/edit-post/toggle-list-view')
    };
  }, []);
  const isLargeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('medium');
  const isWideViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('wide');
  const {
    shouldShowContextualToolbar,
    canFocusHiddenToolbar,
    fixedToolbarCanBeFocused
  } = useShouldContextualToolbarShow(); // If there's a block toolbar to be focused, disable the focus shortcut for the document toolbar.
  // There's a fixed block toolbar when the fixed toolbar option is enabled or when the browser width is less than the large viewport.

  const blockToolbarCanBeFocused = shouldShowContextualToolbar || canFocusHiddenToolbar || fixedToolbarCanBeFocused;
  /* translators: accessibility text for the editor toolbar */

  const toolbarAriaLabel = (0,external_gc_i18n_namespaceObject.__)('文档工具');

  const toggleListView = (0,external_gc_element_namespaceObject.useCallback)(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  const overflowItems = (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ToolbarItem, {
    as: external_gc_components_namespaceObject.Button,
    className: "edit-post-header-toolbar__document-overview-toggle",
    icon: list_view,
    disabled: isTextModeEnabled,
    isPressed: isListViewOpen
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: (0,external_gc_i18n_namespaceObject.__)('文档概述'),
    onClick: toggleListView,
    shortcut: listViewShortcut,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }));
  const toggleInserter = (0,external_gc_element_namespaceObject.useCallback)(() => {
    if (isInserterOpened) {
      // Focusing the inserter button should close the inserter popover.
      // However, there are some cases it won't close when the focus is lost.
      // See https://github.com/GeChiUI/gutenberg/issues/43090 for more details.
      inserterButton.current.focus();
      setIsInserterOpened(false);
    } else {
      setIsInserterOpened(true);
    }
  }, [isInserterOpened, setIsInserterOpened]);
  /* translators: button label text should, if possible, be under 16 characters. */

  const longLabel = (0,external_gc_i18n_namespaceObject._x)('Toggle block inserter', 'Generic label for block inserter button');

  const shortLabel = !isInserterOpened ? (0,external_gc_i18n_namespaceObject.__)('添加') : (0,external_gc_i18n_namespaceObject.__)('关闭');
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.NavigableToolbar, {
    className: "edit-post-header-toolbar",
    "aria-label": toolbarAriaLabel,
    shouldUseKeyboardFocusShortcut: !blockToolbarCanBeFocused
  }, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-header-toolbar__left"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ToolbarItem, {
    ref: inserterButton,
    as: external_gc_components_namespaceObject.Button,
    className: "edit-post-header-toolbar__inserter-toggle",
    variant: "primary",
    isPressed: isInserterOpened,
    onMouseDown: preventDefault,
    onClick: toggleInserter,
    disabled: !isInserterEnabled,
    icon: library_plus,
    label: showIconLabels ? shortLabel : longLabel,
    showTooltip: !showIconLabels
  }), (isWideViewport || !showIconLabels) && (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, isLargeViewport && !hasFixedToolbar && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ToolbarItem, {
    as: external_gc_blockEditor_namespaceObject.ToolSelector,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined,
    disabled: isTextModeEnabled
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ToolbarItem, {
    as: external_gc_editor_namespaceObject.EditorHistoryUndo,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ToolbarItem, {
    as: external_gc_editor_namespaceObject.EditorHistoryRedo,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), overflowItems)));
}

/* harmony default export */ var header_toolbar = (HeaderToolbar);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/mode-switcher/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Set of available mode options.
 *
 * @type {Array}
 */

const MODES = [{
  value: 'visual',
  label: (0,external_gc_i18n_namespaceObject.__)('可视化编辑器')
}, {
  value: 'text',
  label: (0,external_gc_i18n_namespaceObject.__)('代码编辑器')
}];

function ModeSwitcher() {
  const {
    shortcut,
    isRichEditingEnabled,
    isCodeEditingEnabled,
    isEditingTemplate,
    mode
  } = (0,external_gc_data_namespaceObject.useSelect)(select => ({
    shortcut: select(external_gc_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-post/toggle-mode'),
    isRichEditingEnabled: select(external_gc_editor_namespaceObject.store).getEditorSettings().richEditingEnabled,
    isCodeEditingEnabled: select(external_gc_editor_namespaceObject.store).getEditorSettings().codeEditingEnabled,
    isEditingTemplate: select(store_store).isEditingTemplate(),
    mode: select(store_store).getEditorMode()
  }), []);
  const {
    switchEditorMode
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);

  if (isEditingTemplate) {
    return null;
  }

  if (!isRichEditingEnabled || !isCodeEditingEnabled) {
    return null;
  }

  const choices = MODES.map(choice => {
    if (choice.value !== mode) {
      return { ...choice,
        shortcut
      };
    }

    return choice;
  });
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuGroup, {
    label: (0,external_gc_i18n_namespaceObject.__)('编辑')
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItemsChoice, {
    choices: choices,
    value: mode,
    onSelect: switchEditorMode
  }));
}

/* harmony default export */ var mode_switcher = (ModeSwitcher);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/preferences-menu-item/index.js


/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */


function PreferencesMenuItem() {
  const {
    openModal
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    onClick: () => {
      openModal(PREFERENCES_MODAL_NAME);
    }
  }, (0,external_gc_i18n_namespaceObject.__)('偏好设置'));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/writing-menu/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */



function WritingMenu() {
  const registry = (0,external_gc_data_namespaceObject.useRegistry)();
  const isDistractionFree = (0,external_gc_data_namespaceObject.useSelect)(select => select(external_gc_blockEditor_namespaceObject.store).getSettings().isDistractionFree, []);
  const {
    setIsInserterOpened,
    setIsListViewOpened,
    closeGeneralSidebar
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    set: setPreference
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_preferences_namespaceObject.store);

  const toggleDistractionFree = () => {
    registry.batch(() => {
      setPreference('core/edit-post', 'fixedToolbar', false);
      setIsInserterOpened(false);
      setIsListViewOpened(false);
      closeGeneralSidebar();
    });
  };

  const isLargeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('medium');

  if (!isLargeViewport) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuGroup, {
    label: (0,external_gc_i18n_namespaceObject._x)('视图', 'noun')
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    disabled: isDistractionFree,
    name: "fixedToolbar",
    label: (0,external_gc_i18n_namespaceObject.__)('顶部工具栏'),
    info: (0,external_gc_i18n_namespaceObject.__)('在一个位置访问所有的区块和文档工具'),
    messageActivated: (0,external_gc_i18n_namespaceObject.__)('顶部工具栏已启用'),
    messageDeactivated: (0,external_gc_i18n_namespaceObject.__)('顶部工具栏已禁用')
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: "focusMode",
    label: (0,external_gc_i18n_namespaceObject.__)('探照灯模式'),
    info: (0,external_gc_i18n_namespaceObject.__)('集中注意力在一个区块上'),
    messageActivated: (0,external_gc_i18n_namespaceObject.__)('探照灯模式已启用'),
    messageDeactivated: (0,external_gc_i18n_namespaceObject.__)('探照灯模式已禁用')
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: "fullscreenMode",
    label: (0,external_gc_i18n_namespaceObject.__)('全屏模式'),
    info: (0,external_gc_i18n_namespaceObject.__)('显示和隐藏管理UI'),
    messageActivated: (0,external_gc_i18n_namespaceObject.__)('全屏模式已启用'),
    messageDeactivated: (0,external_gc_i18n_namespaceObject.__)('全屏模式已禁用'),
    shortcut: external_gc_keycodes_namespaceObject.displayShortcut.secondary('f')
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_preferences_namespaceObject.PreferenceToggleMenuItem, {
    scope: "core/edit-post",
    name: "distractionFree",
    onToggle: toggleDistractionFree,
    label: (0,external_gc_i18n_namespaceObject.__)('专注'),
    info: (0,external_gc_i18n_namespaceObject.__)('平静地写作'),
    messageActivated: (0,external_gc_i18n_namespaceObject.__)('专注写作模式已开启'),
    messageDeactivated: (0,external_gc_i18n_namespaceObject.__)('专注写作模式已关闭'),
    shortcut: external_gc_keycodes_namespaceObject.displayShortcut.primaryShift('\\')
  }));
}

/* harmony default export */ var writing_menu = (WritingMenu);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/more-menu/index.js


/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */






const MoreMenu = ({
  showIconLabels
}) => {
  const isLargeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('large');
  return (0,external_gc_element_namespaceObject.createElement)(MoreMenuDropdown, {
    toggleProps: {
      showTooltip: !showIconLabels,
      ...(showIconLabels && {
        variant: 'tertiary'
      })
    }
  }, ({
    onClose
  }) => (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, showIconLabels && !isLargeViewport && (0,external_gc_element_namespaceObject.createElement)(pinned_items.Slot, {
    className: showIconLabels && 'show-icon-labels',
    scope: "core/edit-post"
  }), (0,external_gc_element_namespaceObject.createElement)(writing_menu, null), (0,external_gc_element_namespaceObject.createElement)(mode_switcher, null), (0,external_gc_element_namespaceObject.createElement)(action_item.Slot, {
    name: "core/edit-post/plugin-more-menu",
    label: (0,external_gc_i18n_namespaceObject.__)('插件'),
    as: external_gc_components_namespaceObject.MenuGroup,
    fillProps: {
      onClick: onClose
    }
  }), (0,external_gc_element_namespaceObject.createElement)(tools_more_menu_group.Slot, {
    fillProps: {
      onClose
    }
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuGroup, null, (0,external_gc_element_namespaceObject.createElement)(PreferencesMenuItem, null))));
};

/* harmony default export */ var more_menu = (MoreMenu);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/post-publish-button-or-toggle.js


/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */


function PostPublishButtonOrToggle({
  forceIsDirty,
  forceIsSaving,
  hasPublishAction,
  isBeingScheduled,
  isPending,
  isPublished,
  isPublishSidebarEnabled,
  isPublishSidebarOpened,
  isScheduled,
  togglePublishSidebar,
  setEntitiesSavedStatesCallback
}) {
  const IS_TOGGLE = 'toggle';
  const IS_BUTTON = 'button';
  const isSmallerThanMediumViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('medium', '<');
  let component;
  /**
   * Conditions to show a BUTTON (publish directly) or a TOGGLE (open publish sidebar):
   *
   * 1) We want to show a BUTTON when the post status is at the _final stage_
   * for a particular role (see https://www.gechiui.com/support/post-status/):
   *
   * - is published
   * - is scheduled to be published
   * - is pending and can't be published (but only for viewports >= medium).
   * 	 Originally, we considered showing a button for pending posts that couldn't be published
   * 	 (for example, for an author with the contributor role). Some languages can have
   * 	 long translations for "Submit for review", so given the lack of UI real estate available
   * 	 we decided to take into account the viewport in that case.
   *  	 See: https://github.com/GeChiUI/gutenberg/issues/10475
   *
   * 2) Then, in small viewports, we'll show a TOGGLE.
   *
   * 3) Finally, we'll use the publish sidebar status to decide:
   *
   * - if it is enabled, we show a TOGGLE
   * - if it is disabled, we show a BUTTON
   */

  if (isPublished || isScheduled && isBeingScheduled || isPending && !hasPublishAction && !isSmallerThanMediumViewport) {
    component = IS_BUTTON;
  } else if (isSmallerThanMediumViewport) {
    component = IS_TOGGLE;
  } else if (isPublishSidebarEnabled) {
    component = IS_TOGGLE;
  } else {
    component = IS_BUTTON;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostPublishButton, {
    forceIsDirty: forceIsDirty,
    forceIsSaving: forceIsSaving,
    isOpen: isPublishSidebarOpened,
    isToggle: component === IS_TOGGLE,
    onToggle: togglePublishSidebar,
    setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
  });
}
/* harmony default export */ var post_publish_button_or_toggle = ((0,external_gc_compose_namespaceObject.compose)((0,external_gc_data_namespaceObject.withSelect)(select => {
  var _select$getCurrentPos;

  return {
    hasPublishAction: (_select$getCurrentPos = select(external_gc_editor_namespaceObject.store).getCurrentPost()?._links?.['gc:action-publish']) !== null && _select$getCurrentPos !== void 0 ? _select$getCurrentPos : false,
    isBeingScheduled: select(external_gc_editor_namespaceObject.store).isEditedPostBeingScheduled(),
    isPending: select(external_gc_editor_namespaceObject.store).isCurrentPostPending(),
    isPublished: select(external_gc_editor_namespaceObject.store).isCurrentPostPublished(),
    isPublishSidebarEnabled: select(external_gc_editor_namespaceObject.store).isPublishSidebarEnabled(),
    isPublishSidebarOpened: select(store_store).isPublishSidebarOpened(),
    isScheduled: select(external_gc_editor_namespaceObject.store).isCurrentPostScheduled()
  };
}), (0,external_gc_data_namespaceObject.withDispatch)(dispatch => {
  const {
    togglePublishSidebar
  } = dispatch(store_store);
  return {
    togglePublishSidebar
  };
}))(PostPublishButtonOrToggle));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/device-preview/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */


function DevicePreview() {
  const {
    hasActiveMetaboxes,
    isPostSaveable,
    isSaving,
    isViewable,
    deviceType
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    var _postType$viewable;

    const {
      getEditedPostAttribute
    } = select(external_gc_editor_namespaceObject.store);
    const {
      getPostType
    } = select(external_gc_coreData_namespaceObject.store);
    const postType = getPostType(getEditedPostAttribute('type'));
    return {
      hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
      isSaving: select(store_store).isSavingMetaBoxes(),
      isPostSaveable: select(external_gc_editor_namespaceObject.store).isEditedPostSaveable(),
      isViewable: (_postType$viewable = postType?.viewable) !== null && _postType$viewable !== void 0 ? _postType$viewable : false,
      deviceType: select(store_store).__experimentalGetPreviewDeviceType()
    };
  }, []);
  const {
    __experimentalSetPreviewDeviceType: setPreviewDeviceType
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__experimentalPreviewOptions, {
    isEnabled: isPostSaveable,
    className: "edit-post-post-preview-dropdown",
    deviceType: deviceType,
    setDeviceType: setPreviewDeviceType,
    label: (0,external_gc_i18n_namespaceObject.__)('预览')
  }, isViewable && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuGroup, null, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-header-preview__grouping-external"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostPreviewButton, {
    className: 'edit-post-header-preview__button-external',
    role: "menuitem",
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined,
    textContent: (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_i18n_namespaceObject.__)('在新标签页中预览'), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Icon, {
      icon: library_external
    }))
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/view-link/index.js


/**
 * GeChiUI dependencies
 */






function ViewLink() {
  const {
    permalink,
    isPublished,
    label
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    // Grab post type to retrieve the view_item label.
    const postTypeSlug = select(external_gc_editor_namespaceObject.store).getCurrentPostType();
    const postType = select(external_gc_coreData_namespaceObject.store).getPostType(postTypeSlug);
    return {
      permalink: select(external_gc_editor_namespaceObject.store).getPermalink(),
      isPublished: select(external_gc_editor_namespaceObject.store).isCurrentPostPublished(),
      label: postType?.labels.view_item
    };
  }, []); // Only render the view button if the post is published and has a permalink.

  if (!isPublished || !permalink) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    icon: library_external,
    label: label || (0,external_gc_i18n_namespaceObject.__)('查看文章'),
    href: permalink,
    target: "_blank"
  });
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/main-dashboard-button/index.js


/**
 * GeChiUI dependencies
 */

const slotName = '__experimentalMainDashboardButton';
const {
  Fill: main_dashboard_button_Fill,
  Slot: MainDashboardButtonSlot
} = (0,external_gc_components_namespaceObject.createSlotFill)(slotName);
const MainDashboardButton = main_dashboard_button_Fill;

const main_dashboard_button_Slot = ({
  children
}) => {
  const fills = (0,external_gc_components_namespaceObject.__experimentalUseSlotFills)(slotName);
  const hasFills = Boolean(fills && fills.length);

  if (!hasFills) {
    return children;
  }

  return (0,external_gc_element_namespaceObject.createElement)(MainDashboardButtonSlot, {
    bubblesVirtually: true
  });
};

MainDashboardButton.Slot = main_dashboard_button_Slot;
/* harmony default export */ var main_dashboard_button = (MainDashboardButton);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/chevron-right-small.js


/**
 * GeChiUI dependencies
 */

const chevronRightSmall = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M10.8622 8.04053L14.2805 12.0286L10.8622 16.0167L9.72327 15.0405L12.3049 12.0286L9.72327 9.01672L10.8622 8.04053Z"
}));
/* harmony default export */ var chevron_right_small = (chevronRightSmall);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/chevron-left-small.js


/**
 * GeChiUI dependencies
 */

const chevronLeftSmall = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "m13.1 16-3.4-4 3.4-4 1.1 1-2.6 3 2.6 3-1.1 1z"
}));
/* harmony default export */ var chevron_left_small = (chevronLeftSmall);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/layout.js


/**
 * GeChiUI dependencies
 */

const layout = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ var library_layout = (layout);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/document-actions/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */



function DocumentActions() {
  const {
    template,
    isEditing
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      isEditingTemplate,
      getEditedPostTemplate
    } = select(store_store);

    const _isEditing = isEditingTemplate();

    return {
      template: _isEditing ? getEditedPostTemplate() : null,
      isEditing: _isEditing
    };
  }, []);
  const {
    clearSelectedBlock
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_blockEditor_namespaceObject.store);
  const {
    setIsEditingTemplate
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    open: openCommandCenter
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_commands_namespaceObject.store);

  if (!isEditing || !template) {
    return null;
  }

  let templateTitle = (0,external_gc_i18n_namespaceObject.__)('默认');

  if (template?.title) {
    templateTitle = template.title;
  } else if (!!template) {
    templateTitle = template.slug;
  }

  return (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-document-actions"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "edit-post-document-actions__back",
    onClick: () => {
      clearSelectedBlock();
      setIsEditingTemplate(false);
    },
    icon: (0,external_gc_i18n_namespaceObject.isRTL)() ? chevron_right_small : chevron_left_small
  }, (0,external_gc_i18n_namespaceObject.__)('返回')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "edit-post-document-actions__command",
    onClick: () => openCommandCenter()
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalHStack, {
    className: "edit-post-document-actions__title",
    spacing: 1,
    justify: "center"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockIcon, {
    icon: library_layout
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalText, {
    size: "body",
    as: "h1"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.VisuallyHidden, {
    as: "span"
  }, (0,external_gc_i18n_namespaceObject.__)('编辑模板：%s')), templateTitle)), (0,external_gc_element_namespaceObject.createElement)("span", {
    className: "edit-post-document-actions__shortcut"
  }, external_gc_keycodes_namespaceObject.displayShortcut.primary('k'))));
}

/* harmony default export */ var document_actions = (DocumentActions);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */










const slideY = {
  hidden: {
    y: '-50px'
  },
  hover: {
    y: 0,
    transition: {
      type: 'tween',
      delay: 0.2
    }
  }
};
const slideX = {
  hidden: {
    x: '-100%'
  },
  hover: {
    x: 0,
    transition: {
      type: 'tween',
      delay: 0.2
    }
  }
};

function Header({
  setEntitiesSavedStatesCallback
}) {
  const isLargeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('large');
  const {
    hasActiveMetaboxes,
    isPublishSidebarOpened,
    isSaving,
    showIconLabels
  } = (0,external_gc_data_namespaceObject.useSelect)(select => ({
    hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
    isPublishSidebarOpened: select(store_store).isPublishSidebarOpened(),
    isSaving: select(store_store).isSavingMetaBoxes(),
    showIconLabels: select(store_store).isFeatureActive('showIconLabels')
  }), []);
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-header"
  }, (0,external_gc_element_namespaceObject.createElement)(main_dashboard_button.Slot, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__unstableMotion.div, {
    variants: slideX,
    transition: {
      type: 'tween',
      delay: 0.8
    }
  }, (0,external_gc_element_namespaceObject.createElement)(fullscreen_mode_close, {
    showTooltip: true
  }))), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__unstableMotion.div, {
    variants: slideY,
    transition: {
      type: 'tween',
      delay: 0.8
    },
    className: "edit-post-header__toolbar"
  }, (0,external_gc_element_namespaceObject.createElement)(header_toolbar, null), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-header__center"
  }, (0,external_gc_element_namespaceObject.createElement)(document_actions, null))), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__unstableMotion.div, {
    variants: slideY,
    transition: {
      type: 'tween',
      delay: 0.8
    },
    className: "edit-post-header__settings"
  }, !isPublishSidebarOpened && // This button isn't completely hidden by the publish sidebar.
  // We can't hide the whole toolbar when the publish sidebar is open because
  // we want to prevent mounting/unmounting the PostPublishButtonOrToggle DOM node.
  // We track that DOM node to return focus to the PostPublishButtonOrToggle
  // when the publish sidebar has been closed.
  (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSavedState, {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    showIconLabels: showIconLabels
  }), (0,external_gc_element_namespaceObject.createElement)(DevicePreview, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostPreviewButton, {
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined
  }), (0,external_gc_element_namespaceObject.createElement)(ViewLink, null), (0,external_gc_element_namespaceObject.createElement)(post_publish_button_or_toggle, {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
  }), (isLargeViewport || !showIconLabels) && (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(pinned_items.Slot, {
    scope: "core/edit-post"
  }), (0,external_gc_element_namespaceObject.createElement)(more_menu, {
    showIconLabels: showIconLabels
  })), showIconLabels && !isLargeViewport && (0,external_gc_element_namespaceObject.createElement)(more_menu, {
    showIconLabels: showIconLabels
  })));
}

/* harmony default export */ var header = (Header);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/close.js


/**
 * GeChiUI dependencies
 */

const close_close = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ var library_close = (close_close);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/secondary-sidebar/inserter-sidebar.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */


function InserterSidebar() {
  const {
    insertionPoint,
    showMostUsedBlocks
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      isFeatureActive,
      __experimentalGetInsertionPoint
    } = select(store_store);
    return {
      insertionPoint: __experimentalGetInsertionPoint(),
      showMostUsedBlocks: isFeatureActive('mostUsedBlocks')
    };
  }, []);
  const {
    setIsInserterOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const isMobileViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('medium', '<');
  const TagName = !isMobileViewport ? external_gc_components_namespaceObject.VisuallyHidden : 'div';
  const [inserterDialogRef, inserterDialogProps] = (0,external_gc_compose_namespaceObject.__experimentalUseDialog)({
    onClose: () => setIsInserterOpened(false),
    focusOnMount: null
  });
  const libraryRef = (0,external_gc_element_namespaceObject.useRef)();
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    libraryRef.current.focusSearch();
  }, []);
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    ref: inserterDialogRef,
    ...inserterDialogProps,
    className: "edit-post-editor__inserter-panel"
  }, (0,external_gc_element_namespaceObject.createElement)(TagName, {
    className: "edit-post-editor__inserter-panel-header"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    icon: library_close,
    label: (0,external_gc_i18n_namespaceObject.__)('关闭区块插入器'),
    onClick: () => setIsInserterOpened(false)
  })), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-editor__inserter-panel-content"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__experimentalLibrary, {
    showMostUsedBlocks: showMostUsedBlocks,
    showInserterHelpPanel: true,
    shouldFocusBlock: isMobileViewport,
    rootClientId: insertionPoint.rootClientId,
    __experimentalInsertionIndex: insertionPoint.insertionIndex,
    __experimentalFilterValue: insertionPoint.filterValue,
    ref: libraryRef
  })));
}

;// CONCATENATED MODULE: external ["gc","dom"]
var external_gc_dom_namespaceObject = window["gc"]["dom"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/secondary-sidebar/list-view-outline.js


/**
 * GeChiUI dependencies
 */






function EmptyOutlineIllustration() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.SVG, {
    width: "138",
    height: "148",
    viewBox: "0 0 138 148",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Rect, {
    width: "138",
    height: "148",
    rx: "4",
    fill: "#F0F6FC"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Line, {
    x1: "44",
    y1: "28",
    x2: "24",
    y2: "28",
    stroke: "#DDDDDD"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Rect, {
    x: "48",
    y: "16",
    width: "27",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Path, {
    d: "M54.7585 32V23.2727H56.6037V26.8736H60.3494V23.2727H62.1903V32H60.3494V28.3949H56.6037V32H54.7585ZM67.4574 23.2727V32H65.6122V25.0241H65.5611L63.5625 26.277V24.6406L65.723 23.2727H67.4574Z",
    fill: "black"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Line, {
    x1: "55",
    y1: "59",
    x2: "24",
    y2: "59",
    stroke: "#DDDDDD"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Rect, {
    x: "59",
    y: "47",
    width: "29",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Path, {
    d: "M65.7585 63V54.2727H67.6037V57.8736H71.3494V54.2727H73.1903V63H71.3494V59.3949H67.6037V63H65.7585ZM74.6605 63V61.6705L77.767 58.794C78.0313 58.5384 78.2528 58.3082 78.4318 58.1037C78.6136 57.8991 78.7514 57.6989 78.8452 57.5028C78.9389 57.304 78.9858 57.0895 78.9858 56.8594C78.9858 56.6037 78.9276 56.3835 78.8111 56.1989C78.6946 56.0114 78.5355 55.8679 78.3338 55.7685C78.1321 55.6662 77.9034 55.6151 77.6477 55.6151C77.3807 55.6151 77.1477 55.669 76.9489 55.777C76.75 55.8849 76.5966 56.0398 76.4886 56.2415C76.3807 56.4432 76.3267 56.6832 76.3267 56.9616H74.5753C74.5753 56.3906 74.7045 55.8949 74.9631 55.4744C75.2216 55.054 75.5838 54.7287 76.0497 54.4986C76.5156 54.2685 77.0526 54.1534 77.6605 54.1534C78.2855 54.1534 78.8295 54.2642 79.2926 54.4858C79.7585 54.7045 80.1207 55.0085 80.3793 55.3977C80.6378 55.7869 80.767 56.233 80.767 56.7358C80.767 57.0653 80.7017 57.3906 80.571 57.7116C80.4432 58.0327 80.2145 58.3892 79.8849 58.7812C79.5554 59.1705 79.0909 59.6378 78.4915 60.1832L77.2173 61.4318V61.4915H80.8821V63H74.6605Z",
    fill: "black"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Line, {
    x1: "80",
    y1: "90",
    x2: "24",
    y2: "90",
    stroke: "#DDDDDD"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Rect, {
    x: "84",
    y: "78",
    width: "30",
    height: "23",
    rx: "4",
    fill: "#F0B849"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Path, {
    d: "M90.7585 94V85.2727H92.6037V88.8736H96.3494V85.2727H98.1903V94H96.3494V90.3949H92.6037V94H90.7585ZM99.5284 92.4659V91.0128L103.172 85.2727H104.425V87.2841H103.683L101.386 90.919V90.9872H106.564V92.4659H99.5284ZM103.717 94V92.0227L103.751 91.3793V85.2727H105.482V94H103.717Z",
    fill: "black"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Line, {
    x1: "66",
    y1: "121",
    x2: "24",
    y2: "121",
    stroke: "#DDDDDD"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Rect, {
    x: "70",
    y: "109",
    width: "29",
    height: "23",
    rx: "4",
    fill: "#DDDDDD"
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Path, {
    d: "M76.7585 125V116.273H78.6037V119.874H82.3494V116.273H84.1903V125H82.3494V121.395H78.6037V125H76.7585ZM88.8864 125.119C88.25 125.119 87.6832 125.01 87.1861 124.791C86.6918 124.57 86.3011 124.266 86.0142 123.879C85.7301 123.49 85.5838 123.041 85.5753 122.533H87.4332C87.4446 122.746 87.5142 122.933 87.642 123.095C87.7727 123.254 87.946 123.378 88.1619 123.466C88.3778 123.554 88.6207 123.598 88.8906 123.598C89.1719 123.598 89.4205 123.548 89.6364 123.449C89.8523 123.349 90.0213 123.212 90.1435 123.036C90.2656 122.859 90.3267 122.656 90.3267 122.426C90.3267 122.193 90.2614 121.987 90.1307 121.808C90.0028 121.626 89.8182 121.484 89.5767 121.382C89.3381 121.28 89.054 121.229 88.7244 121.229H87.9105V119.874H88.7244C89.0028 119.874 89.2486 119.825 89.4616 119.729C89.6776 119.632 89.8452 119.499 89.9645 119.328C90.0838 119.155 90.1435 118.953 90.1435 118.723C90.1435 118.504 90.0909 118.312 89.9858 118.148C89.8835 117.98 89.7386 117.849 89.5511 117.756C89.3665 117.662 89.1506 117.615 88.9034 117.615C88.6534 117.615 88.4247 117.661 88.2173 117.751C88.0099 117.839 87.8438 117.966 87.7188 118.131C87.5938 118.295 87.527 118.489 87.5185 118.71H85.75C85.7585 118.207 85.902 117.764 86.1804 117.381C86.4588 116.997 86.8338 116.697 87.3054 116.482C87.7798 116.263 88.3153 116.153 88.9119 116.153C89.5142 116.153 90.0412 116.263 90.4929 116.482C90.9446 116.7 91.2955 116.996 91.5455 117.368C91.7983 117.737 91.9233 118.152 91.9205 118.612C91.9233 119.101 91.7713 119.509 91.4645 119.835C91.1605 120.162 90.7642 120.369 90.2756 120.457V120.526C90.9176 120.608 91.4063 120.831 91.7415 121.195C92.0795 121.555 92.2472 122.007 92.2443 122.55C92.2472 123.047 92.1037 123.489 91.8139 123.875C91.527 124.261 91.1307 124.565 90.625 124.787C90.1193 125.009 89.5398 125.119 88.8864 125.119Z",
    fill: "black"
  }));
}

function ListViewOutline() {
  const {
    headingCount
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getGlobalBlockCount
    } = select(external_gc_blockEditor_namespaceObject.store);
    return {
      headingCount: getGlobalBlockCount('core/heading')
    };
  }, []);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-editor__list-view-overview"
  }, (0,external_gc_element_namespaceObject.createElement)("div", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalText, null, (0,external_gc_i18n_namespaceObject.__)('字符数：')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalText, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.CharacterCount, null))), (0,external_gc_element_namespaceObject.createElement)("div", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalText, null, (0,external_gc_i18n_namespaceObject.__)('字数：')), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.WordCount, null)), (0,external_gc_element_namespaceObject.createElement)("div", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalText, null, (0,external_gc_i18n_namespaceObject.__)('阅读时长：')), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.TimeToRead, null))), headingCount > 0 ? (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.DocumentOutline, null) : (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-editor__list-view-empty-headings"
  }, (0,external_gc_element_namespaceObject.createElement)(EmptyOutlineIllustration, null), (0,external_gc_element_namespaceObject.createElement)("p", null, (0,external_gc_i18n_namespaceObject.__)('导航文档结构，解决标题级别为空或不正确等问题。'))));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/secondary-sidebar/list-view-sidebar.js


/**
 * GeChiUI dependencies
 */










/**
 * Internal dependencies
 */



function ListViewSidebar() {
  const {
    setIsListViewOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store); // This hook handles focus when the sidebar first renders.

  const focusOnMountRef = (0,external_gc_compose_namespaceObject.useFocusOnMount)('firstElement'); // The next 2 hooks handle focus for when the sidebar closes and returning focus to the element that had focus before sidebar opened.

  const headerFocusReturnRef = (0,external_gc_compose_namespaceObject.useFocusReturn)();
  const contentFocusReturnRef = (0,external_gc_compose_namespaceObject.useFocusReturn)();

  function closeOnEscape(event) {
    if (event.keyCode === external_gc_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
      event.preventDefault();
      setIsListViewOpened(false);
    }
  } // Use internal state instead of a ref to make sure that the component
  // re-renders when the dropZoneElement updates.


  const [dropZoneElement, setDropZoneElement] = (0,external_gc_element_namespaceObject.useState)(null); // Tracks our current tab.

  const [tab, setTab] = (0,external_gc_element_namespaceObject.useState)('list-view'); // This ref refers to the sidebar as a whole.

  const sidebarRef = (0,external_gc_element_namespaceObject.useRef)(); // This ref refers to the tab panel.

  const tabPanelRef = (0,external_gc_element_namespaceObject.useRef)(); // This ref refers to the list view application area.

  const listViewRef = (0,external_gc_element_namespaceObject.useRef)(); // Must merge the refs together so focus can be handled properly in the next function.

  const listViewContainerRef = (0,external_gc_compose_namespaceObject.useMergeRefs)([contentFocusReturnRef, focusOnMountRef, listViewRef, setDropZoneElement]);
  /*
   * Callback function to handle list view or outline focus.
   *
   * @param {string} currentTab The current tab. Either list view or outline.
   *
   * @return void
   */

  function handleSidebarFocus(currentTab) {
    // Tab panel focus.
    const tabPanelFocus = external_gc_dom_namespaceObject.focus.tabbable.find(tabPanelRef.current)[0]; // List view tab is selected.

    if (currentTab === 'list-view') {
      // Either focus the list view or the tab panel. Must have a fallback because the list view does not render when there are no blocks.
      const listViewApplicationFocus = external_gc_dom_namespaceObject.focus.tabbable.find(listViewRef.current)[0];
      const listViewFocusArea = sidebarRef.current.contains(listViewApplicationFocus) ? listViewApplicationFocus : tabPanelFocus;
      listViewFocusArea.focus(); // Outline tab is selected.
    } else {
      tabPanelFocus.focus();
    }
  } // This only fires when the sidebar is open because of the conditional rendering. It is the same shortcut to open but that is defined as a global shortcut and only fires when the sidebar is closed.


  (0,external_gc_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-post/toggle-list-view', () => {
    // If the sidebar has focus, it is safe to close.
    if (sidebarRef.current.contains(sidebarRef.current.ownerDocument.activeElement)) {
      setIsListViewOpened(false); // If the list view or outline does not have focus, focus should be moved to it.
    } else {
      handleSidebarFocus(tab);
    }
  });
  /**
   * Render tab content for a given tab name.
   *
   * @param {string} tabName The name of the tab to render.
   */

  function renderTabContent(tabName) {
    if (tabName === 'list-view') {
      return (0,external_gc_element_namespaceObject.createElement)("div", {
        className: "edit-post-editor__list-view-panel-content"
      }, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__experimentalListView, {
        dropZoneElement: dropZoneElement
      }));
    }

    return (0,external_gc_element_namespaceObject.createElement)(ListViewOutline, null);
  }

  return (// eslint-disable-next-line jsx-a11y/no-static-element-interactions
    (0,external_gc_element_namespaceObject.createElement)("div", {
      className: "edit-post-editor__document-overview-panel",
      onKeyDown: closeOnEscape,
      ref: sidebarRef
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
      className: "edit-post-editor__document-overview-panel__close-button",
      ref: headerFocusReturnRef,
      icon: close_small,
      label: (0,external_gc_i18n_namespaceObject.__)('关闭'),
      onClick: () => setIsListViewOpened(false)
    }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.TabPanel, {
      className: "edit-post-editor__document-overview-panel__tab-panel",
      ref: tabPanelRef,
      onSelect: tabName => setTab(tabName),
      selectOnMove: false,
      tabs: [{
        name: 'list-view',
        title: (0,external_gc_i18n_namespaceObject._x)('列表视图', 'Post overview'),
        className: 'edit-post-sidebar__panel-tab'
      }, {
        name: 'outline',
        title: (0,external_gc_i18n_namespaceObject._x)('大纲', 'Post overview'),
        className: 'edit-post-sidebar__panel-tab'
      }]
    }, currentTab => (0,external_gc_element_namespaceObject.createElement)("div", {
      className: "edit-post-editor__list-view-container",
      ref: listViewContainerRef
    }, renderTabContent(currentTab.name))))
  );
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/drawer-left.js


/**
 * GeChiUI dependencies
 */

const drawerLeft = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8.5 18.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h2.5v13zm10-.5c0 .3-.2.5-.5.5h-8v-13h8c.3 0 .5.2.5.5v12z"
}));
/* harmony default export */ var drawer_left = (drawerLeft);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/drawer-right.js


/**
 * GeChiUI dependencies
 */

const drawerRight = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4 14.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h8v13zm4.5-.5c0 .3-.2.5-.5.5h-2.5v-13H18c.3 0 .5.2.5.5v12z"
}));
/* harmony default export */ var drawer_right = (drawerRight);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/settings-header/index.js


/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */



const SettingsHeader = ({
  sidebarName
}) => {
  const {
    openGeneralSidebar
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);

  const openDocumentSettings = () => openGeneralSidebar('edit-post/document');

  const openBlockSettings = () => openGeneralSidebar('edit-post/block');

  const {
    documentLabel,
    isTemplateMode
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const postTypeLabel = select(external_gc_editor_namespaceObject.store).getPostTypeLabel();
    return {
      // translators: Default label for the Document sidebar tab, not selected.
      documentLabel: postTypeLabel || (0,external_gc_i18n_namespaceObject._x)('文档', 'noun'),
      isTemplateMode: select(store_store).isEditingTemplate()
    };
  }, []);
  const [documentAriaLabel, documentActiveClass] = sidebarName === 'edit-post/document' ? // translators: ARIA label for the Document sidebar tab, selected. %s: Document label.
  [(0,external_gc_i18n_namespaceObject.sprintf)((0,external_gc_i18n_namespaceObject.__)('%s（已选中）'), documentLabel), 'is-active'] : [documentLabel, ''];
  const [blockAriaLabel, blockActiveClass] = sidebarName === 'edit-post/block' ? // translators: ARIA label for the Block Settings Sidebar tab, selected.
  [(0,external_gc_i18n_namespaceObject.__)('区块（已选中）'), 'is-active'] : // translators: ARIA label for the Block Settings Sidebar tab, not selected.
  [(0,external_gc_i18n_namespaceObject.__)('区块'), ''];
  const [templateAriaLabel, templateActiveClass] = sidebarName === 'edit-post/document' ? [(0,external_gc_i18n_namespaceObject.__)('模板（已选中）'), 'is-active'] : [(0,external_gc_i18n_namespaceObject.__)('模板'), ''];
  /* Use a list so screen readers will announce how many tabs there are. */

  return (0,external_gc_element_namespaceObject.createElement)("ul", null, !isTemplateMode && (0,external_gc_element_namespaceObject.createElement)("li", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    onClick: openDocumentSettings,
    className: `edit-post-sidebar__panel-tab ${documentActiveClass}`,
    "aria-label": documentAriaLabel,
    "data-label": documentLabel
  }, documentLabel)), isTemplateMode && (0,external_gc_element_namespaceObject.createElement)("li", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    onClick: openDocumentSettings,
    className: `edit-post-sidebar__panel-tab ${templateActiveClass}`,
    "aria-label": templateAriaLabel,
    "data-label": (0,external_gc_i18n_namespaceObject.__)('模板')
  }, (0,external_gc_i18n_namespaceObject.__)('模板'))), (0,external_gc_element_namespaceObject.createElement)("li", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    onClick: openBlockSettings,
    className: `edit-post-sidebar__panel-tab ${blockActiveClass}`,
    "aria-label": blockAriaLabel // translators: Data label for the Block Settings Sidebar tab.
    ,
    "data-label": (0,external_gc_i18n_namespaceObject.__)('区块')
  }, // translators: Text label for the Block Settings Sidebar tab.
  (0,external_gc_i18n_namespaceObject.__)('区块'))));
};

/* harmony default export */ var settings_header = (SettingsHeader);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-visibility/index.js


/**
 * GeChiUI dependencies
 */




function PostVisibility() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_gc_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_gc_element_namespaceObject.useMemo)(() => ({
    // Anchor the popover to the middle of the entire row so that it doesn't
    // move around when the label changes.
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostVisibilityCheck, {
    render: ({
      canEdit
    }) => (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
      ref: setPopoverAnchor,
      className: "edit-post-post-visibility"
    }, (0,external_gc_element_namespaceObject.createElement)("span", null, (0,external_gc_i18n_namespaceObject.__)('可见性')), !canEdit && (0,external_gc_element_namespaceObject.createElement)("span", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostVisibilityLabel, null)), canEdit && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Dropdown, {
      contentClassName: "edit-post-post-visibility__dialog",
      popoverProps: popoverProps,
      focusOnMount: true,
      renderToggle: ({
        isOpen,
        onToggle
      }) => (0,external_gc_element_namespaceObject.createElement)(PostVisibilityToggle, {
        isOpen: isOpen,
        onClick: onToggle
      }),
      renderContent: ({
        onClose
      }) => (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostVisibility, {
        onClose: onClose
      })
    }))
  });
}

function PostVisibilityToggle({
  isOpen,
  onClick
}) {
  const label = (0,external_gc_editor_namespaceObject.usePostVisibilityLabel)();
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "edit-post-post-visibility__toggle",
    variant: "tertiary",
    "aria-expanded": isOpen // translators: %s: Current post visibility.
    ,
    "aria-label": (0,external_gc_i18n_namespaceObject.sprintf)((0,external_gc_i18n_namespaceObject.__)('选择可见性：%s'), label),
    onClick: onClick
  }, label);
}

/* harmony default export */ var post_visibility = (PostVisibility);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-trash/index.js


/**
 * GeChiUI dependencies
 */

function PostTrash() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTrashCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTrash, null));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-schedule/index.js


/**
 * GeChiUI dependencies
 */




function PostSchedule() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_gc_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_gc_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostScheduleCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
    className: "edit-post-post-schedule",
    ref: setPopoverAnchor
  }, (0,external_gc_element_namespaceObject.createElement)("span", null, (0,external_gc_i18n_namespaceObject.__)('发布')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    contentClassName: "edit-post-post-schedule__dialog",
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_gc_element_namespaceObject.createElement)(PostScheduleToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSchedule, {
      onClose: onClose
    })
  })));
}

function PostScheduleToggle({
  isOpen,
  onClick
}) {
  const label = (0,external_gc_editor_namespaceObject.usePostScheduleLabel)();
  const fullLabel = (0,external_gc_editor_namespaceObject.usePostScheduleLabel)({
    full: true
  });
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "edit-post-post-schedule__toggle",
    variant: "tertiary",
    label: fullLabel,
    showTooltip: true,
    "aria-expanded": isOpen // translators: %s: Current post date.
    ,
    "aria-label": (0,external_gc_i18n_namespaceObject.sprintf)((0,external_gc_i18n_namespaceObject.__)('更改日期：%s'), label),
    onClick: onClick
  }, label);
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-sticky/index.js


/**
 * GeChiUI dependencies
 */


function PostSticky() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostStickyCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSticky, null)));
}
/* harmony default export */ var post_sticky = (PostSticky);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-author/index.js


/**
 * GeChiUI dependencies
 */


function PostAuthor() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostAuthorCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
    className: "edit-post-post-author"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostAuthor, null)));
}
/* harmony default export */ var post_author = (PostAuthor);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-slug/index.js


/**
 * GeChiUI dependencies
 */


function PostSlug() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSlugCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
    className: "edit-post-post-slug"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSlug, null)));
}
/* harmony default export */ var post_slug = (PostSlug);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-format/index.js


/**
 * GeChiUI dependencies
 */


function PostFormat() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostFormatCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
    className: "edit-post-post-format"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostFormat, null)));
}
/* harmony default export */ var post_format = (PostFormat);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-pending-status/index.js


/**
 * GeChiUI dependencies
 */


function PostPendingStatus() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostPendingStatusCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostPendingStatus, null)));
}
/* harmony default export */ var post_pending_status = (PostPendingStatus);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js


/**
 * Defines as extensibility slot for the Summary panel.
 */

/**
 * GeChiUI dependencies
 */

const {
  Fill: plugin_post_status_info_Fill,
  Slot: plugin_post_status_info_Slot
} = (0,external_gc_components_namespaceObject.createSlotFill)('PluginPostStatusInfo');
/**
 * Renders a row in the Summary panel of the Document sidebar.
 * It should be noted that this is named and implemented around the function it serves
 * and not its location, which may change in future iterations.
 *
 * @param {Object}    props             Component properties.
 * @param {string}    [props.className] An optional class name added to the row.
 * @param {GCElement} props.children    Children to be rendered.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = gc.i18n.__;
 * var PluginPostStatusInfo = gc.editPost.PluginPostStatusInfo;
 *
 * function MyPluginPostStatusInfo() {
 * 	return gc.element.createElement(
 * 		PluginPostStatusInfo,
 * 		{
 * 			className: 'my-plugin-post-status-info',
 * 		},
 * 		__( '我的文章状态信息' )
 * 	)
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@gechiui/i18n';
 * import { PluginPostStatusInfo } from '@gechiui/edit-post';
 *
 * const MyPluginPostStatusInfo = () => (
 * 	<PluginPostStatusInfo
 * 		className="my-plugin-post-status-info"
 * 	>
 * 		{ __( '我的文章状态信息' ) }
 * 	</PluginPostStatusInfo>
 * );
 * ```
 *
 * @return {GCComponent} The component to be rendered.
 */

const PluginPostStatusInfo = ({
  children,
  className
}) => (0,external_gc_element_namespaceObject.createElement)(plugin_post_status_info_Fill, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
  className: className
}, children));

PluginPostStatusInfo.Slot = plugin_post_status_info_Slot;
/* harmony default export */ var plugin_post_status_info = (PluginPostStatusInfo);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/add-template.js


/**
 * GeChiUI dependencies
 */

const addTemplate = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18.5 5.5V8H20V5.5H22.5V4H20V1.5H18.5V4H16V5.5H18.5ZM13.9624 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V10.0391H18.5V18C18.5 18.2761 18.2761 18.5 18 18.5H10L10 10.4917L16.4589 10.5139L16.4641 9.01389L5.5 8.97618V6C5.5 5.72386 5.72386 5.5 6 5.5H13.9624V4ZM5.5 10.4762V18C5.5 18.2761 5.72386 18.5 6 18.5H8.5L8.5 10.4865L5.5 10.4762Z"
}));
/* harmony default export */ var add_template = (addTemplate);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-template/create-modal.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */



const DEFAULT_TITLE = (0,external_gc_i18n_namespaceObject.__)('自定义模板');

function PostTemplateCreateModal({
  onClose
}) {
  const defaultBlockTemplate = (0,external_gc_data_namespaceObject.useSelect)(select => select(external_gc_editor_namespaceObject.store).getEditorSettings().defaultBlockTemplate, []);
  const {
    __unstableCreateTemplate,
    __unstableSwitchToTemplateMode
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const [title, setTitle] = (0,external_gc_element_namespaceObject.useState)('');
  const [isBusy, setIsBusy] = (0,external_gc_element_namespaceObject.useState)(false);

  const cancel = () => {
    setTitle('');
    onClose();
  };

  const submit = async event => {
    event.preventDefault();

    if (isBusy) {
      return;
    }

    setIsBusy(true);
    const newTemplateContent = defaultBlockTemplate !== null && defaultBlockTemplate !== void 0 ? defaultBlockTemplate : (0,external_gc_blocks_namespaceObject.serialize)([(0,external_gc_blocks_namespaceObject.createBlock)('core/group', {
      tagName: 'header',
      layout: {
        inherit: true
      }
    }, [(0,external_gc_blocks_namespaceObject.createBlock)('core/site-title'), (0,external_gc_blocks_namespaceObject.createBlock)('core/site-tagline')]), (0,external_gc_blocks_namespaceObject.createBlock)('core/separator'), (0,external_gc_blocks_namespaceObject.createBlock)('core/group', {
      tagName: 'main'
    }, [(0,external_gc_blocks_namespaceObject.createBlock)('core/group', {
      layout: {
        inherit: true
      }
    }, [(0,external_gc_blocks_namespaceObject.createBlock)('core/post-title')]), (0,external_gc_blocks_namespaceObject.createBlock)('core/post-content', {
      layout: {
        inherit: true
      }
    })])]);
    await __unstableCreateTemplate({
      slug: (0,external_gc_url_namespaceObject.cleanForSlug)(title || DEFAULT_TITLE),
      content: newTemplateContent,
      title: title || DEFAULT_TITLE
    });
    setIsBusy(false);
    cancel();

    __unstableSwitchToTemplateMode(true);
  };

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Modal, {
    title: (0,external_gc_i18n_namespaceObject.__)('创建自定义模板'),
    onRequestClose: cancel,
    className: "edit-post-post-template__create-modal"
  }, (0,external_gc_element_namespaceObject.createElement)("form", {
    className: "edit-post-post-template__create-form",
    onSubmit: submit
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalVStack, {
    spacing: "3"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_gc_i18n_namespaceObject.__)('名称'),
    value: title,
    onChange: setTitle,
    placeholder: DEFAULT_TITLE,
    disabled: isBusy,
    help: (0,external_gc_i18n_namespaceObject.__)('描述模板，例如：“文章带侧边栏“。一个自定义模板可以手动应用于任何文章或页面。')
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalHStack, {
    justify: "right"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: cancel
  }, (0,external_gc_i18n_namespaceObject.__)('取消')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit",
    isBusy: isBusy,
    "aria-disabled": isBusy
  }, (0,external_gc_i18n_namespaceObject.__)('创建'))))));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-template/form.js


/**
 * GeChiUI dependencies
 */








/**
 * Internal dependencies
 */



function PostTemplateForm({
  onClose
}) {
  var _options$find, _selectedOption$value;

  const {
    isPostsPage,
    availableTemplates,
    fetchedTemplates,
    selectedTemplateSlug,
    canCreate,
    canEdit
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      canUser,
      getEntityRecord,
      getEntityRecords
    } = select(external_gc_coreData_namespaceObject.store);
    const editorSettings = select(external_gc_editor_namespaceObject.store).getEditorSettings();
    const siteSettings = canUser('read', 'settings') ? getEntityRecord('root', 'site') : undefined;

    const _isPostsPage = select(external_gc_editor_namespaceObject.store).getCurrentPostId() === siteSettings?.page_for_posts;

    const canCreateTemplates = canUser('create', 'templates');
    return {
      isPostsPage: _isPostsPage,
      availableTemplates: editorSettings.availableTemplates,
      fetchedTemplates: canCreateTemplates ? getEntityRecords('postType', 'gc_template', {
        post_type: select(external_gc_editor_namespaceObject.store).getCurrentPostType(),
        per_page: -1
      }) : undefined,
      selectedTemplateSlug: select(external_gc_editor_namespaceObject.store).getEditedPostAttribute('template'),
      canCreate: canCreateTemplates && !_isPostsPage && editorSettings.supportsTemplateMode,
      canEdit: canCreateTemplates && editorSettings.supportsTemplateMode && !!select(store_store).getEditedPostTemplate()
    };
  }, []);
  const options = (0,external_gc_element_namespaceObject.useMemo)(() => Object.entries({ ...availableTemplates,
    ...Object.fromEntries((fetchedTemplates !== null && fetchedTemplates !== void 0 ? fetchedTemplates : []).map(({
      slug,
      title
    }) => [slug, title.rendered]))
  }).map(([slug, title]) => ({
    value: slug,
    label: title
  })), [availableTemplates, fetchedTemplates]);
  const selectedOption = (_options$find = options.find(option => option.value === selectedTemplateSlug)) !== null && _options$find !== void 0 ? _options$find : options.find(option => !option.value); // The default option has '' value.

  const {
    editPost
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_editor_namespaceObject.store);
  const {
    __unstableSwitchToTemplateMode
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const [isCreateModalOpen, setIsCreateModalOpen] = (0,external_gc_element_namespaceObject.useState)(false);
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-post-template__form"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__experimentalInspectorPopoverHeader, {
    title: (0,external_gc_i18n_namespaceObject.__)('模板'),
    help: (0,external_gc_i18n_namespaceObject.__)('模板定义了查看系统时内容的显示方式。'),
    actions: canCreate ? [{
      icon: add_template,
      label: (0,external_gc_i18n_namespaceObject.__)('添加模板'),
      onClick: () => setIsCreateModalOpen(true)
    }] : [],
    onClose: onClose
  }), isPostsPage ? (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Notice, {
    className: "edit-post-post-template__notice",
    status: "warning",
    isDismissible: false
  }, (0,external_gc_i18n_namespaceObject.__)('文章页面模版无法更改。')) : (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.SelectControl, {
    __nextHasNoMarginBottom: true,
    hideLabelFromVision: true,
    label: (0,external_gc_i18n_namespaceObject.__)('模板'),
    value: (_selectedOption$value = selectedOption?.value) !== null && _selectedOption$value !== void 0 ? _selectedOption$value : '',
    options: options,
    onChange: slug => editPost({
      template: slug || ''
    })
  }), canEdit && (0,external_gc_element_namespaceObject.createElement)("p", null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    variant: "link",
    onClick: () => __unstableSwitchToTemplateMode()
  }, (0,external_gc_i18n_namespaceObject.__)('编辑模板'))), isCreateModalOpen && (0,external_gc_element_namespaceObject.createElement)(PostTemplateCreateModal, {
    onClose: () => setIsCreateModalOpen(false)
  }));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-template/index.js


/**
 * GeChiUI dependencies
 */






/**
 * Internal dependencies
 */



function PostTemplate() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_gc_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_gc_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  const isVisible = (0,external_gc_data_namespaceObject.useSelect)(select => {
    var _select$canUser;

    const postTypeSlug = select(external_gc_editor_namespaceObject.store).getCurrentPostType();
    const postType = select(external_gc_coreData_namespaceObject.store).getPostType(postTypeSlug);

    if (!postType?.viewable) {
      return false;
    }

    const settings = select(external_gc_editor_namespaceObject.store).getEditorSettings();
    const hasTemplates = !!settings.availableTemplates && Object.keys(settings.availableTemplates).length > 0;

    if (hasTemplates) {
      return true;
    }

    if (!settings.supportsTemplateMode) {
      return false;
    }

    const canCreateTemplates = (_select$canUser = select(external_gc_coreData_namespaceObject.store).canUser('create', 'templates')) !== null && _select$canUser !== void 0 ? _select$canUser : false;
    return canCreateTemplates;
  }, []);

  if (!isVisible) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
    className: "edit-post-post-template",
    ref: setPopoverAnchor
  }, (0,external_gc_element_namespaceObject.createElement)("span", null, (0,external_gc_i18n_namespaceObject.__)('模板')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    className: "edit-post-post-template__dropdown",
    contentClassName: "edit-post-post-template__dialog",
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_gc_element_namespaceObject.createElement)(PostTemplateToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_gc_element_namespaceObject.createElement)(PostTemplateForm, {
      onClose: onClose
    })
  }));
}

function PostTemplateToggle({
  isOpen,
  onClick
}) {
  const templateTitle = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const templateSlug = select(external_gc_editor_namespaceObject.store).getEditedPostAttribute('template');
    const {
      supportsTemplateMode,
      availableTemplates
    } = select(external_gc_editor_namespaceObject.store).getEditorSettings();

    if (!supportsTemplateMode && availableTemplates[templateSlug]) {
      return availableTemplates[templateSlug];
    }

    const template = select(external_gc_coreData_namespaceObject.store).canUser('create', 'templates') && select(store_store).getEditedPostTemplate();
    return template?.title || template?.slug || availableTemplates?.[templateSlug];
  }, []);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "edit-post-post-template__toggle",
    variant: "tertiary",
    "aria-expanded": isOpen,
    "aria-label": templateTitle ? (0,external_gc_i18n_namespaceObject.sprintf)( // translators: %s: Name of the currently selected template.
    (0,external_gc_i18n_namespaceObject.__)('选择模板：%s'), templateTitle) : (0,external_gc_i18n_namespaceObject.__)('选择模板'),
    onClick: onClick
  }, templateTitle !== null && templateTitle !== void 0 ? templateTitle : (0,external_gc_i18n_namespaceObject.__)('默认模板'));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-url/index.js


/**
 * GeChiUI dependencies
 */




function PostURL() {
  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = (0,external_gc_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_gc_element_namespaceObject.useMemo)(() => ({
    anchor: popoverAnchor,
    placement: 'bottom-end'
  }), [popoverAnchor]);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostURLCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, {
    className: "edit-post-post-url",
    ref: setPopoverAnchor
  }, (0,external_gc_element_namespaceObject.createElement)("span", null, (0,external_gc_i18n_namespaceObject.__)('URL')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    className: "edit-post-post-url__dropdown",
    contentClassName: "edit-post-post-url__dialog",
    focusOnMount: true,
    renderToggle: ({
      isOpen,
      onToggle
    }) => (0,external_gc_element_namespaceObject.createElement)(PostURLToggle, {
      isOpen: isOpen,
      onClick: onToggle
    }),
    renderContent: ({
      onClose
    }) => (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostURL, {
      onClose: onClose
    })
  })));
}

function PostURLToggle({
  isOpen,
  onClick
}) {
  const label = (0,external_gc_editor_namespaceObject.usePostURLLabel)();
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    className: "edit-post-post-url__toggle",
    variant: "tertiary",
    "aria-expanded": isOpen // translators: %s: Current post URL.
    ,
    "aria-label": (0,external_gc_i18n_namespaceObject.sprintf)((0,external_gc_i18n_namespaceObject.__)('更改 URL：%s'), label),
    onClick: onClick
  }, label);
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-status/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */













/**
 * Module Constants
 */

const PANEL_NAME = 'post-status';

function PostStatus({
  isOpened,
  onTogglePanel
}) {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    className: "edit-post-post-status",
    title: (0,external_gc_i18n_namespaceObject.__)('摘要'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_gc_element_namespaceObject.createElement)(plugin_post_status_info.Slot, null, fills => (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(post_visibility, null), (0,external_gc_element_namespaceObject.createElement)(PostSchedule, null), (0,external_gc_element_namespaceObject.createElement)(PostTemplate, null), (0,external_gc_element_namespaceObject.createElement)(PostURL, null), (0,external_gc_element_namespaceObject.createElement)(post_sticky, null), (0,external_gc_element_namespaceObject.createElement)(post_pending_status, null), (0,external_gc_element_namespaceObject.createElement)(post_format, null), (0,external_gc_element_namespaceObject.createElement)(post_slug, null), (0,external_gc_element_namespaceObject.createElement)(post_author, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSyncStatus, null), fills, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalHStack, {
    style: {
      marginTop: '16px'
    },
    spacing: 4,
    wrap: true
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSwitchToDraftButton, null), (0,external_gc_element_namespaceObject.createElement)(PostTrash, null)))));
}

/* harmony default export */ var post_status = ((0,external_gc_compose_namespaceObject.compose)([(0,external_gc_data_namespaceObject.withSelect)(select => {
  // We use isEditorPanelRemoved to hide the panel if it was programatically removed. We do
  // not use isEditorPanelEnabled since this panel should not be disabled through the UI.
  const {
    isEditorPanelRemoved,
    isEditorPanelOpened
  } = select(store_store);
  return {
    isRemoved: isEditorPanelRemoved(PANEL_NAME),
    isOpened: isEditorPanelOpened(PANEL_NAME)
  };
}), (0,external_gc_compose_namespaceObject.ifCondition)(({
  isRemoved
}) => !isRemoved), (0,external_gc_data_namespaceObject.withDispatch)(dispatch => ({
  onTogglePanel() {
    return dispatch(store_store).toggleEditorPanelOpened(PANEL_NAME);
  }

}))])(PostStatus));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/last-revision/index.js


/**
 * GeChiUI dependencies
 */



function LastRevision() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostLastRevisionCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    className: "edit-post-last-revision__panel"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostLastRevision, null)));
}

/* harmony default export */ var last_revision = (LastRevision);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js


/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */



function TaxonomyPanel({
  isEnabled,
  taxonomy,
  isOpened,
  onTogglePanel,
  children
}) {
  if (!isEnabled) {
    return null;
  }

  const taxonomyMenuName = taxonomy?.labels?.menu_name;

  if (!taxonomyMenuName) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: onTogglePanel
  }, children);
}

/* harmony default export */ var taxonomy_panel = ((0,external_gc_compose_namespaceObject.compose)((0,external_gc_data_namespaceObject.withSelect)((select, ownProps) => {
  const slug = ownProps.taxonomy?.slug;
  const panelName = slug ? `taxonomy-panel-${slug}` : '';
  return {
    panelName,
    isEnabled: slug ? select(store_store).isEditorPanelEnabled(panelName) : false,
    isOpened: slug ? select(store_store).isEditorPanelOpened(panelName) : false
  };
}), (0,external_gc_data_namespaceObject.withDispatch)((dispatch, ownProps) => ({
  onTogglePanel: () => {
    dispatch(store_store).toggleEditorPanelOpened(ownProps.panelName);
  }
})))(TaxonomyPanel));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-taxonomies/index.js


/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */



function PostTaxonomies() {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTaxonomiesCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTaxonomies, {
    taxonomyWrapper: (content, taxonomy) => {
      return (0,external_gc_element_namespaceObject.createElement)(taxonomy_panel, {
        taxonomy: taxonomy
      }, content);
    }
  }));
}

/* harmony default export */ var post_taxonomies = (PostTaxonomies);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/featured-image/index.js


/**
 * GeChiUI dependencies
 */






/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const featured_image_PANEL_NAME = 'featured-image';

function FeaturedImage({
  isEnabled,
  isOpened,
  postType,
  onTogglePanel
}) {
  var _postType$labels$feat;

  if (!isEnabled) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostFeaturedImageCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    title: (_postType$labels$feat = postType?.labels?.featured_image) !== null && _postType$labels$feat !== void 0 ? _postType$labels$feat : (0,external_gc_i18n_namespaceObject.__)('特色图片'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostFeaturedImage, null)));
}

const applyWithSelect = (0,external_gc_data_namespaceObject.withSelect)(select => {
  const {
    getEditedPostAttribute
  } = select(external_gc_editor_namespaceObject.store);
  const {
    getPostType
  } = select(external_gc_coreData_namespaceObject.store);
  const {
    isEditorPanelEnabled,
    isEditorPanelOpened
  } = select(store_store);
  return {
    postType: getPostType(getEditedPostAttribute('type')),
    isEnabled: isEditorPanelEnabled(featured_image_PANEL_NAME),
    isOpened: isEditorPanelOpened(featured_image_PANEL_NAME)
  };
});
const applyWithDispatch = (0,external_gc_data_namespaceObject.withDispatch)(dispatch => {
  const {
    toggleEditorPanelOpened
  } = dispatch(store_store);
  return {
    onTogglePanel: (...args) => toggleEditorPanelOpened(featured_image_PANEL_NAME, ...args)
  };
});
/* harmony default export */ var featured_image = ((0,external_gc_compose_namespaceObject.compose)(applyWithSelect, applyWithDispatch)(FeaturedImage));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/post-excerpt/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const post_excerpt_PANEL_NAME = 'post-excerpt';

function PostExcerpt({
  isEnabled,
  isOpened,
  onTogglePanel
}) {
  if (!isEnabled) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostExcerptCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    title: (0,external_gc_i18n_namespaceObject.__)('摘要'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostExcerpt, null)));
}

/* harmony default export */ var post_excerpt = ((0,external_gc_compose_namespaceObject.compose)([(0,external_gc_data_namespaceObject.withSelect)(select => {
  return {
    isEnabled: select(store_store).isEditorPanelEnabled(post_excerpt_PANEL_NAME),
    isOpened: select(store_store).isEditorPanelOpened(post_excerpt_PANEL_NAME)
  };
}), (0,external_gc_data_namespaceObject.withDispatch)(dispatch => ({
  onTogglePanel() {
    return dispatch(store_store).toggleEditorPanelOpened(post_excerpt_PANEL_NAME);
  }

}))])(PostExcerpt));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/discussion-panel/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const discussion_panel_PANEL_NAME = 'discussion-panel';

function DiscussionPanel({
  isEnabled,
  isOpened,
  onTogglePanel
}) {
  if (!isEnabled) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTypeSupportCheck, {
    supportKeys: ['comments', 'trackbacks']
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    title: (0,external_gc_i18n_namespaceObject.__)('讨论'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTypeSupportCheck, {
    supportKeys: "comments"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostComments, null))), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostTypeSupportCheck, {
    supportKeys: "trackbacks"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostPingbacks, null)))));
}

/* harmony default export */ var discussion_panel = ((0,external_gc_compose_namespaceObject.compose)([(0,external_gc_data_namespaceObject.withSelect)(select => {
  return {
    isEnabled: select(store_store).isEditorPanelEnabled(discussion_panel_PANEL_NAME),
    isOpened: select(store_store).isEditorPanelOpened(discussion_panel_PANEL_NAME)
  };
}), (0,external_gc_data_namespaceObject.withDispatch)(dispatch => ({
  onTogglePanel() {
    return dispatch(store_store).toggleEditorPanelOpened(discussion_panel_PANEL_NAME);
  }

}))])(DiscussionPanel));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/page-attributes/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const page_attributes_PANEL_NAME = 'page-attributes';
function PageAttributes() {
  var _postType$labels$attr;

  const {
    isEnabled,
    isOpened,
    postType
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostAttribute
    } = select(external_gc_editor_namespaceObject.store);
    const {
      isEditorPanelEnabled,
      isEditorPanelOpened
    } = select(store_store);
    const {
      getPostType
    } = select(external_gc_coreData_namespaceObject.store);
    return {
      isEnabled: isEditorPanelEnabled(page_attributes_PANEL_NAME),
      isOpened: isEditorPanelOpened(page_attributes_PANEL_NAME),
      postType: getPostType(getEditedPostAttribute('type'))
    };
  }, []);
  const {
    toggleEditorPanelOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);

  if (!isEnabled || !postType) {
    return null;
  }

  const onTogglePanel = (...args) => toggleEditorPanelOpened(page_attributes_PANEL_NAME, ...args);

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PageAttributesCheck, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    title: (_postType$labels$attr = postType?.labels?.attributes) !== null && _postType$labels$attr !== void 0 ? _postType$labels$attr : (0,external_gc_i18n_namespaceObject.__)('页面属性'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PageAttributesParent, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelRow, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PageAttributesOrder, null))));
}
/* harmony default export */ var page_attributes = (PageAttributes);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */


/**
 * Render metabox area.
 *
 * @param {Object} props          Component props.
 * @param {string} props.location metabox location.
 * @return {GCComponent} The component to be rendered.
 */

function MetaBoxesArea({
  location
}) {
  const container = (0,external_gc_element_namespaceObject.useRef)(null);
  const formRef = (0,external_gc_element_namespaceObject.useRef)(null);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    formRef.current = document.querySelector('.metabox-location-' + location);

    if (formRef.current) {
      container.current.appendChild(formRef.current);
    }

    return () => {
      if (formRef.current) {
        document.querySelector('#metaboxes').appendChild(formRef.current);
      }
    };
  }, [location]);
  const isSaving = (0,external_gc_data_namespaceObject.useSelect)(select => {
    return select(store_store).isSavingMetaBoxes();
  }, []);
  const classes = classnames_default()('edit-post-meta-boxes-area', `is-${location}`, {
    'is-loading': isSaving
  });
  return (0,external_gc_element_namespaceObject.createElement)("div", {
    className: classes
  }, isSaving && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Spinner, null), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-meta-boxes-area__container",
    ref: container
  }), (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-meta-boxes-area__clear"
  }));
}

/* harmony default export */ var meta_boxes_area = (MetaBoxesArea);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/meta-boxes/meta-box-visibility.js
/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */



class MetaBoxVisibility extends external_gc_element_namespaceObject.Component {
  componentDidMount() {
    this.updateDOM();
  }

  componentDidUpdate(prevProps) {
    if (this.props.isVisible !== prevProps.isVisible) {
      this.updateDOM();
    }
  }

  updateDOM() {
    const {
      id,
      isVisible
    } = this.props;
    const element = document.getElementById(id);

    if (!element) {
      return;
    }

    if (isVisible) {
      element.classList.remove('is-hidden');
    } else {
      element.classList.add('is-hidden');
    }
  }

  render() {
    return null;
  }

}

/* harmony default export */ var meta_box_visibility = ((0,external_gc_data_namespaceObject.withSelect)((select, {
  id
}) => ({
  isVisible: select(store_store).isEditorPanelEnabled(`meta-box-${id}`)
}))(MetaBoxVisibility));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/meta-boxes/index.js


/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */




function MetaBoxes({
  location
}) {
  const registry = (0,external_gc_data_namespaceObject.useRegistry)();
  const {
    metaBoxes,
    areMetaBoxesInitialized,
    isEditorReady
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      __unstableIsEditorReady
    } = select(external_gc_editor_namespaceObject.store);
    const {
      getMetaBoxesPerLocation,
      areMetaBoxesInitialized: _areMetaBoxesInitialized
    } = select(store_store);
    return {
      metaBoxes: getMetaBoxesPerLocation(location),
      areMetaBoxesInitialized: _areMetaBoxesInitialized(),
      isEditorReady: __unstableIsEditorReady()
    };
  }, [location]); // When editor is ready, initialize postboxes (gc core script) and metabox
  // saving. This initializes all meta box locations, not just this specific
  // one.

  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (isEditorReady && !areMetaBoxesInitialized) {
      registry.dispatch(store_store).initializeMetaBoxes();
    }
  }, [isEditorReady, areMetaBoxesInitialized]);

  if (!areMetaBoxesInitialized) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (metaBoxes !== null && metaBoxes !== void 0 ? metaBoxes : []).map(({
    id
  }) => (0,external_gc_element_namespaceObject.createElement)(meta_box_visibility, {
    key: id,
    id: id
  })), (0,external_gc_element_namespaceObject.createElement)(meta_boxes_area, {
    location: location
  }));
}

;// CONCATENATED MODULE: external ["gc","warning"]
var external_gc_warning_namespaceObject = window["gc"]["warning"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/plugin-document-setting-panel/index.js


/**
 * Defines as extensibility slot for the Settings sidebar
 */

/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */



const {
  Fill: plugin_document_setting_panel_Fill,
  Slot: plugin_document_setting_panel_Slot
} = (0,external_gc_components_namespaceObject.createSlotFill)('PluginDocumentSettingPanel');

const PluginDocumentSettingFill = ({
  isEnabled,
  panelName,
  opened,
  onToggle,
  className,
  title,
  icon,
  children
}) => {
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(enable_plugin_document_setting_panel, {
    label: title,
    panelName: panelName
  }), (0,external_gc_element_namespaceObject.createElement)(plugin_document_setting_panel_Fill, null, isEnabled && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
    className: className,
    title: title,
    icon: icon,
    opened: opened,
    onToggle: onToggle
  }, children)));
};
/**
 * Renders items below the Status & Availability panel in the Document Sidebar.
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.name]                          The machine-friendly name for the panel.
 * @param {string}                [props.className]                     An optional class name added to the row.
 * @param {string}                [props.title]                         The title of the panel
 * @param {GCBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.gechiui.com/resource/dashicons/) icon slug string, or an SVG GC element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var el = gc.element.createElement;
 * var __ = gc.i18n.__;
 * var registerPlugin = gc.plugins.registerPlugin;
 * var PluginDocumentSettingPanel = gc.editPost.PluginDocumentSettingPanel;
 *
 * function MyDocumentSettingPlugin() {
 * 	return el(
 * 		PluginDocumentSettingPanel,
 * 		{
 * 			className: 'my-document-setting-plugin',
 * 			title: 'My Panel',
 * 		},
 * 		__( '我的文档设置台' )
 * 	);
 * }
 *
 * registerPlugin( 'my-document-setting-plugin', {
 * 		render: MyDocumentSettingPlugin
 * } );
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { registerPlugin } from '@gechiui/plugins';
 * import { PluginDocumentSettingPanel } from '@gechiui/edit-post';
 *
 * const MyDocumentSettingTest = () => (
 * 		<PluginDocumentSettingPanel className="my-document-setting-plugin" title="My Panel">
 *			<p>My Document Setting Panel</p>
 *		</PluginDocumentSettingPanel>
 *	);
 *
 *  registerPlugin( 'document-setting-test', { render: MyDocumentSettingTest } );
 * ```
 *
 * @return {GCComponent} The component to be rendered.
 */


const PluginDocumentSettingPanel = (0,external_gc_compose_namespaceObject.compose)((0,external_gc_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  if (undefined === ownProps.name) {
    typeof process !== "undefined" && process.env && "production" !== "production" ? 0 : void 0;
  }

  return {
    panelName: `${context.name}/${ownProps.name}`
  };
}), (0,external_gc_data_namespaceObject.withSelect)((select, {
  panelName
}) => {
  return {
    opened: select(store_store).isEditorPanelOpened(panelName),
    isEnabled: select(store_store).isEditorPanelEnabled(panelName)
  };
}), (0,external_gc_data_namespaceObject.withDispatch)((dispatch, {
  panelName
}) => ({
  onToggle() {
    return dispatch(store_store).toggleEditorPanelOpened(panelName);
  }

})))(PluginDocumentSettingFill);
PluginDocumentSettingPanel.Slot = plugin_document_setting_panel_Slot;
/* harmony default export */ var plugin_document_setting_panel = (PluginDocumentSettingPanel);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/plugin-sidebar/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Renders a sidebar when activated. The contents within the `PluginSidebar` will appear as content within the sidebar.
 * It also automatically renders a corresponding `PluginSidebarMenuItem` component when `isPinnable` flag is set to `true`.
 * If you wish to display the sidebar, you can with use the `PluginSidebarMoreMenuItem` component or the `gc.data.dispatch` API:
 *
 * ```js
 * gc.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'plugin-name/sidebar-name' );
 * ```
 *
 * @see PluginSidebarMoreMenuItem
 *
 * @param {Object}                props                                 Element props.
 * @param {string}                props.name                            A string identifying the sidebar. Must be unique for every sidebar registered within the scope of your plugin.
 * @param {string}                [props.className]                     An optional class name added to the sidebar body.
 * @param {string}                props.title                           Title displayed at the top of the sidebar.
 * @param {boolean}               [props.isPinnable=true]               Whether to allow to pin sidebar to the toolbar. When set to `true` it also automatically renders a corresponding menu item.
 * @param {GCBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.gechiui.com/resource/dashicons/) icon slug string, or an SVG GC element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = gc.i18n.__;
 * var el = gc.element.createElement;
 * var PanelBody = gc.components.PanelBody;
 * var PluginSidebar = gc.editPost.PluginSidebar;
 * var moreIcon = gc.element.createElement( 'svg' ); //... svg element.
 *
 * function MyPluginSidebar() {
 * 	return el(
 * 			PluginSidebar,
 * 			{
 * 				name: 'my-sidebar',
 * 				title: '我的侧边栏标题',
 * 				icon: moreIcon,
 * 			},
 * 			el(
 * 				PanelBody,
 * 				{},
 * 				__( '我的侧边栏内容' )
 * 			)
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@gechiui/i18n';
 * import { PanelBody } from '@gechiui/components';
 * import { PluginSidebar } from '@gechiui/edit-post';
 * import { more } from '@gechiui/icons';
 *
 * const MyPluginSidebar = () => (
 * 	<PluginSidebar
 * 		name="my-sidebar"
 * 		title="My sidebar title"
 * 		icon={ more }
 * 	>
 * 		<PanelBody>
 * 			{ __( '我的侧边栏内容' ) }
 * 		</PanelBody>
 * 	</PluginSidebar>
 * );
 * ```
 */

function PluginSidebarEditPost({
  className,
  ...props
}) {
  const {
    postTitle,
    shortcut,
    showIconLabels
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    return {
      postTitle: select(external_gc_editor_namespaceObject.store).getEditedPostAttribute('title'),
      shortcut: select(external_gc_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-post/toggle-sidebar'),
      showIconLabels: select(store_store).isFeatureActive('showIconLabels')
    };
  }, []);
  return (0,external_gc_element_namespaceObject.createElement)(complementary_area, {
    panelClassName: className,
    className: "edit-post-sidebar",
    smallScreenTitle: postTitle || (0,external_gc_i18n_namespaceObject.__)('（无标题）'),
    scope: "core/edit-post",
    toggleShortcut: shortcut,
    showIconLabels: showIconLabels,
    ...props
  });
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/template-summary/index.js


/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */



function TemplateSummary() {
  const template = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostTemplate
    } = select(store_store);
    return getEditedPostTemplate();
  }, []);

  if (!template) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Flex, {
    align: "flex-start",
    gap: "3"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.FlexItem, null, (0,external_gc_element_namespaceObject.createElement)(icon, {
    icon: library_layout
  })), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.FlexBlock, null, (0,external_gc_element_namespaceObject.createElement)("h2", {
    className: "edit-post-template-summary__title"
  }, template?.title || template?.slug), (0,external_gc_element_namespaceObject.createElement)("p", null, template?.description))));
}

/* harmony default export */ var template_summary = (TemplateSummary);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/settings-sidebar/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */














const SIDEBAR_ACTIVE_BY_DEFAULT = external_gc_element_namespaceObject.Platform.select({
  web: true,
  native: false
});

const SettingsSidebar = () => {
  const {
    sidebarName,
    keyboardShortcut,
    isTemplateMode
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    // The settings sidebar is used by the edit-post/document and edit-post/block sidebars.
    // sidebarName represents the sidebar that is active or that should be active when the SettingsSidebar toggle button is pressed.
    // If one of the two sidebars is active the component will contain the content of that sidebar.
    // When neither of the two sidebars is active we can not simply return null, because the PluginSidebarEditPost
    // component, besides being used to render the sidebar, also renders the toggle button. In that case sidebarName
    // should contain the sidebar that will be active when the toggle button is pressed. If a block
    // is selected, that should be edit-post/block otherwise it's edit-post/document.
    let sidebar = select(store).getActiveComplementaryArea(store_store.name);

    if (!['edit-post/document', 'edit-post/block'].includes(sidebar)) {
      if (select(external_gc_blockEditor_namespaceObject.store).getBlockSelectionStart()) {
        sidebar = 'edit-post/block';
      }

      sidebar = 'edit-post/document';
    }

    const shortcut = select(external_gc_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-post/toggle-sidebar');
    return {
      sidebarName: sidebar,
      keyboardShortcut: shortcut,
      isTemplateMode: select(store_store).isEditingTemplate()
    };
  }, []);
  return (0,external_gc_element_namespaceObject.createElement)(PluginSidebarEditPost, {
    identifier: sidebarName,
    header: (0,external_gc_element_namespaceObject.createElement)(settings_header, {
      sidebarName: sidebarName
    }),
    closeLabel: (0,external_gc_i18n_namespaceObject.__)('关闭设置'),
    headerClassName: "edit-post-sidebar__panel-tabs"
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    title: (0,external_gc_i18n_namespaceObject.__)('设置'),
    toggleShortcut: keyboardShortcut,
    icon: (0,external_gc_i18n_namespaceObject.isRTL)() ? drawer_left : drawer_right,
    isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT
  }, !isTemplateMode && sidebarName === 'edit-post/document' && (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(post_status, null), (0,external_gc_element_namespaceObject.createElement)(plugin_document_setting_panel.Slot, null), (0,external_gc_element_namespaceObject.createElement)(last_revision, null), (0,external_gc_element_namespaceObject.createElement)(post_taxonomies, null), (0,external_gc_element_namespaceObject.createElement)(featured_image, null), (0,external_gc_element_namespaceObject.createElement)(post_excerpt, null), (0,external_gc_element_namespaceObject.createElement)(discussion_panel, null), (0,external_gc_element_namespaceObject.createElement)(page_attributes, null), (0,external_gc_element_namespaceObject.createElement)(MetaBoxes, {
    location: "side"
  })), isTemplateMode && sidebarName === 'edit-post/document' && (0,external_gc_element_namespaceObject.createElement)(template_summary, null), sidebarName === 'edit-post/block' && (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockInspector, null));
};

/* harmony default export */ var settings_sidebar = (SettingsSidebar);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js


/**
 * GeChiUI dependencies
 */



const {
  Fill: plugin_post_publish_panel_Fill,
  Slot: plugin_post_publish_panel_Slot
} = (0,external_gc_components_namespaceObject.createSlotFill)('PluginPostPublishPanel');

const PluginPostPublishPanelFill = ({
  children,
  className,
  title,
  initialOpen = false,
  icon
}) => (0,external_gc_element_namespaceObject.createElement)(plugin_post_publish_panel_Fill, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
  className: className,
  initialOpen: initialOpen || !title,
  title: title,
  icon: icon
}, children));
/**
 * Renders provided content to the post-publish panel in the publish flow
 * (side panel that opens after a user publishes the post).
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.className]                     An optional class name added to the panel.
 * @param {string}                [props.title]                         Title displayed at the top of the panel.
 * @param {boolean}               [props.initialOpen=false]             Whether to have the panel initially opened. When no title is provided it is always opened.
 * @param {GCBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.gechiui.com/resource/dashicons/) icon slug string, or an SVG GC element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = gc.i18n.__;
 * var PluginPostPublishPanel = gc.editPost.PluginPostPublishPanel;
 *
 * function MyPluginPostPublishPanel() {
 * 	return gc.element.createElement(
 * 		PluginPostPublishPanel,
 * 		{
 * 			className: 'my-plugin-post-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@gechiui/i18n';
 * import { PluginPostPublishPanel } from '@gechiui/edit-post';
 *
 * const MyPluginPostPublishPanel = () => (
 * 	<PluginPostPublishPanel
 * 		className="my-plugin-post-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 *         { __( 'My panel content' ) }
 * 	</PluginPostPublishPanel>
 * );
 * ```
 *
 * @return {GCComponent} The component to be rendered.
 */


const PluginPostPublishPanel = (0,external_gc_compose_namespaceObject.compose)((0,external_gc_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon
  };
}))(PluginPostPublishPanelFill);
PluginPostPublishPanel.Slot = plugin_post_publish_panel_Slot;
/* harmony default export */ var plugin_post_publish_panel = (PluginPostPublishPanel);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js


/**
 * GeChiUI dependencies
 */



const {
  Fill: plugin_pre_publish_panel_Fill,
  Slot: plugin_pre_publish_panel_Slot
} = (0,external_gc_components_namespaceObject.createSlotFill)('PluginPrePublishPanel');

const PluginPrePublishPanelFill = ({
  children,
  className,
  title,
  initialOpen = false,
  icon
}) => (0,external_gc_element_namespaceObject.createElement)(plugin_pre_publish_panel_Fill, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.PanelBody, {
  className: className,
  initialOpen: initialOpen || !title,
  title: title,
  icon: icon
}, children));
/**
 * Renders provided content to the pre-publish side panel in the publish flow
 * (side panel that opens when a user first pushes "Publish" from the main editor).
 *
 * @param {Object}                props                                 Component props.
 * @param {string}                [props.className]                     An optional class name added to the panel.
 * @param {string}                [props.title]                         Title displayed at the top of the panel.
 * @param {boolean}               [props.initialOpen=false]             Whether to have the panel initially opened.
 *                                                                      When no title is provided it is always opened.
 * @param {GCBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.gechiui.com/resource/dashicons/)
 *                                                                      icon slug string, or an SVG GC element, to be rendered when
 *                                                                      the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = gc.i18n.__;
 * var PluginPrePublishPanel = gc.editPost.PluginPrePublishPanel;
 *
 * function MyPluginPrePublishPanel() {
 * 	return gc.element.createElement(
 * 		PluginPrePublishPanel,
 * 		{
 * 			className: 'my-plugin-pre-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@gechiui/i18n';
 * import { PluginPrePublishPanel } from '@gechiui/edit-post';
 *
 * const MyPluginPrePublishPanel = () => (
 * 	<PluginPrePublishPanel
 * 		className="my-plugin-pre-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 * 	    { __( 'My panel content' ) }
 * 	</PluginPrePublishPanel>
 * );
 * ```
 *
 * @return {GCComponent} The component to be rendered.
 */


const PluginPrePublishPanel = (0,external_gc_compose_namespaceObject.compose)((0,external_gc_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon
  };
}))(PluginPrePublishPanelFill);
PluginPrePublishPanel.Slot = plugin_pre_publish_panel_Slot;
/* harmony default export */ var plugin_pre_publish_panel = (PluginPrePublishPanel);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/layout/actions-panel.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */




const {
  Fill: actions_panel_Fill,
  Slot: actions_panel_Slot
} = (0,external_gc_components_namespaceObject.createSlotFill)('ActionsPanel');
const ActionsPanelFill = (/* unused pure expression or super */ null && (actions_panel_Fill));
function ActionsPanel({
  setEntitiesSavedStatesCallback,
  closeEntitiesSavedStates,
  isEntitiesSavedStatesOpen
}) {
  const {
    closePublishSidebar,
    togglePublishSidebar
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    publishSidebarOpened,
    hasActiveMetaboxes,
    isSavingMetaBoxes,
    hasNonPostEntityChanges
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    return {
      publishSidebarOpened: select(store_store).isPublishSidebarOpened(),
      hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
      isSavingMetaBoxes: select(store_store).isSavingMetaBoxes(),
      hasNonPostEntityChanges: select(external_gc_editor_namespaceObject.store).hasNonPostEntityChanges()
    };
  }, []);
  const openEntitiesSavedStates = (0,external_gc_element_namespaceObject.useCallback)(() => setEntitiesSavedStatesCallback(true), []); // It is ok for these components to be unmounted when not in visual use.
  // We don't want more than one present at a time, decide which to render.

  let unmountableContent;

  if (publishSidebarOpened) {
    unmountableContent = (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostPublishPanel, {
      onClose: closePublishSidebar,
      forceIsDirty: hasActiveMetaboxes,
      forceIsSaving: isSavingMetaBoxes,
      PrePublishExtension: plugin_pre_publish_panel.Slot,
      PostPublishExtension: plugin_post_publish_panel.Slot
    });
  } else if (hasNonPostEntityChanges) {
    unmountableContent = (0,external_gc_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__toggle-entities-saved-states-panel"
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
      variant: "secondary",
      className: "edit-post-layout__toggle-entities-saved-states-panel-button",
      onClick: openEntitiesSavedStates,
      "aria-expanded": false
    }, (0,external_gc_i18n_namespaceObject.__)('打开保存面板')));
  } else {
    unmountableContent = (0,external_gc_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__toggle-publish-panel"
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
      variant: "secondary",
      className: "edit-post-layout__toggle-publish-panel-button",
      onClick: togglePublishSidebar,
      "aria-expanded": false
    }, (0,external_gc_i18n_namespaceObject.__)('打开发布面板')));
  } // Since EntitiesSavedStates controls its own panel, we can keep it
  // always mounted to retain its own component state (such as checkboxes).


  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, isEntitiesSavedStatesOpen && (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.EntitiesSavedStates, {
    close: closeEntitiesSavedStates
  }), (0,external_gc_element_namespaceObject.createElement)(actions_panel_Slot, {
    bubblesVirtually: true
  }), !isEntitiesSavedStatesOpen && unmountableContent);
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/start-page-options/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */



function useStartPatterns() {
  // A pattern is a start pattern if it includes 'core/post-content' in its blockTypes,
  // and it has no postTypes declares and the current post type is page or if
  // the current post type is part of the postTypes declared.
  const {
    blockPatternsWithPostContentBlockType,
    postType
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getPatternsByBlockTypes
    } = select(external_gc_blockEditor_namespaceObject.store);
    const {
      getCurrentPostType
    } = select(external_gc_editor_namespaceObject.store);
    return {
      blockPatternsWithPostContentBlockType: getPatternsByBlockTypes('core/post-content'),
      postType: getCurrentPostType()
    };
  }, []);
  return (0,external_gc_element_namespaceObject.useMemo)(() => {
    // filter patterns without postTypes declared if the current postType is page
    // or patterns that declare the current postType in its post type array.
    return blockPatternsWithPostContentBlockType.filter(pattern => {
      return postType === 'page' && !pattern.postTypes || Array.isArray(pattern.postTypes) && pattern.postTypes.includes(postType);
    });
  }, [postType, blockPatternsWithPostContentBlockType]);
}

function PatternSelection({
  onChoosePattern
}) {
  const blockPatterns = useStartPatterns();
  const shownBlockPatterns = (0,external_gc_compose_namespaceObject.useAsyncList)(blockPatterns);
  const {
    resetEditorBlocks
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_editor_namespaceObject.store);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.__experimentalBlockPatternsList, {
    blockPatterns: blockPatterns,
    shownPatterns: shownBlockPatterns,
    onClickPattern: (_pattern, blocks) => {
      resetEditorBlocks(blocks);
      onChoosePattern();
    }
  });
}

const START_PAGE_MODAL_STATES = {
  INITIAL: 'INITIAL',
  PATTERN: 'PATTERN',
  CLOSED: 'CLOSED'
};
function StartPageOptions() {
  const [modalState, setModalState] = (0,external_gc_element_namespaceObject.useState)(START_PAGE_MODAL_STATES.INITIAL);
  const blockPatterns = useStartPatterns();
  const hasStartPattern = blockPatterns.length > 0;
  const shouldOpenModel = (0,external_gc_data_namespaceObject.useSelect)(select => {
    if (!hasStartPattern || modalState !== START_PAGE_MODAL_STATES.INITIAL) {
      return false;
    }

    const {
      getEditedPostContent,
      isEditedPostSaveable
    } = select(external_gc_editor_namespaceObject.store);
    const {
      isEditingTemplate,
      isFeatureActive
    } = select(store_store);
    return !isEditedPostSaveable() && '' === getEditedPostContent() && !isEditingTemplate();
  }, [modalState, hasStartPattern]);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (shouldOpenModel) {
      setModalState(START_PAGE_MODAL_STATES.PATTERN);
    }
  }, [shouldOpenModel]);

  if (modalState === START_PAGE_MODAL_STATES.INITIAL || modalState === START_PAGE_MODAL_STATES.CLOSED) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Modal, {
    className: "edit-post-start-page-options__modal",
    title: (0,external_gc_i18n_namespaceObject.__)('选择一个样板'),
    isFullScreen: true,
    onRequestClose: () => {
      setModalState(START_PAGE_MODAL_STATES.CLOSED);
    }
  }, (0,external_gc_element_namespaceObject.createElement)("div", {
    className: "edit-post-start-page-options__modal-content"
  }, modalState === START_PAGE_MODAL_STATES.PATTERN && (0,external_gc_element_namespaceObject.createElement)(PatternSelection, {
    onChoosePattern: () => {
      setModalState(START_PAGE_MODAL_STATES.CLOSED);
    }
  })));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/layout/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */












/**
 * Internal dependencies
 */
















const {
  getLayoutStyles
} = unlock(external_gc_blockEditor_namespaceObject.privateApis);
const interfaceLabels = {
  /* translators: accessibility text for the editor top bar landmark region. */
  header: (0,external_gc_i18n_namespaceObject.__)('编辑顶栏'),

  /* translators: accessibility text for the editor content landmark region. */
  body: (0,external_gc_i18n_namespaceObject.__)('编辑器内容'),

  /* translators: accessibility text for the editor settings landmark region. */
  sidebar: (0,external_gc_i18n_namespaceObject.__)('编辑器设置'),

  /* translators: accessibility text for the editor publish landmark region. */
  actions: (0,external_gc_i18n_namespaceObject.__)('编辑器发布'),

  /* translators: accessibility text for the editor footer landmark region. */
  footer: (0,external_gc_i18n_namespaceObject.__)('编辑页脚')
};

function useEditorStyles() {
  const {
    hasThemeStyleSupport,
    editorSettings
  } = (0,external_gc_data_namespaceObject.useSelect)(select => ({
    hasThemeStyleSupport: select(store_store).isFeatureActive('themeStyles'),
    editorSettings: select(external_gc_editor_namespaceObject.store).getEditorSettings()
  }), []); // Compute the default styles.

  return (0,external_gc_element_namespaceObject.useMemo)(() => {
    var _editorSettings$style, _editorSettings$style2;

    const presetStyles = (_editorSettings$style = editorSettings.styles?.filter(style => style.__unstableType && style.__unstableType !== 'theme')) !== null && _editorSettings$style !== void 0 ? _editorSettings$style : [];
    const defaultEditorStyles = [...editorSettings.defaultEditorStyles, ...presetStyles]; // Has theme styles if the theme supports them and if some styles were not preset styles (in which case they're theme styles).

    const hasThemeStyles = hasThemeStyleSupport && presetStyles.length !== ((_editorSettings$style2 = editorSettings.styles?.length) !== null && _editorSettings$style2 !== void 0 ? _editorSettings$style2 : 0); // If theme styles are not present or displayed, ensure that
    // base layout styles are still present in the editor.

    if (!editorSettings.disableLayoutStyles && !hasThemeStyles) {
      defaultEditorStyles.push({
        css: getLayoutStyles({
          style: {},
          selector: 'body',
          hasBlockGapSupport: false,
          hasFallbackGapSupport: true,
          fallbackGapValue: '0.5em'
        })
      });
    }

    return hasThemeStyles ? editorSettings.styles : defaultEditorStyles;
  }, [editorSettings.defaultEditorStyles, editorSettings.disableLayoutStyles, editorSettings.styles, hasThemeStyleSupport]);
}

function Layout() {
  const isMobileViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('medium', '<');
  const isHugeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('huge', '>=');
  const isLargeViewport = (0,external_gc_compose_namespaceObject.useViewportMatch)('large');
  const {
    openGeneralSidebar,
    closeGeneralSidebar,
    setIsInserterOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    createErrorNotice
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_notices_namespaceObject.store);
  const {
    mode,
    isFullscreenActive,
    isRichEditingEnabled,
    sidebarIsOpened,
    hasActiveMetaboxes,
    hasFixedToolbar,
    previousShortcut,
    nextShortcut,
    hasBlockSelected,
    isInserterOpened,
    isListViewOpened,
    showIconLabels,
    isDistractionFree,
    showBlockBreadcrumbs,
    isTemplateMode,
    documentLabel
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getEditorSettings,
      getPostTypeLabel
    } = select(external_gc_editor_namespaceObject.store);
    const editorSettings = getEditorSettings();
    const postTypeLabel = getPostTypeLabel();
    return {
      isTemplateMode: select(store_store).isEditingTemplate(),
      hasFixedToolbar: select(store_store).isFeatureActive('fixedToolbar'),
      sidebarIsOpened: !!(select(store).getActiveComplementaryArea(store_store.name) || select(store_store).isPublishSidebarOpened()),
      isFullscreenActive: select(store_store).isFeatureActive('fullscreenMode'),
      isInserterOpened: select(store_store).isInserterOpened(),
      isListViewOpened: select(store_store).isListViewOpened(),
      mode: select(store_store).getEditorMode(),
      isRichEditingEnabled: editorSettings.richEditingEnabled,
      hasActiveMetaboxes: select(store_store).hasMetaBoxes(),
      previousShortcut: select(external_gc_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-post/previous-region'),
      nextShortcut: select(external_gc_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-post/next-region'),
      showIconLabels: select(store_store).isFeatureActive('showIconLabels'),
      isDistractionFree: select(store_store).isFeatureActive('distractionFree'),
      showBlockBreadcrumbs: select(store_store).isFeatureActive('showBlockBreadcrumbs'),
      // translators: Default label for the Document in the Block Breadcrumb.
      documentLabel: postTypeLabel || (0,external_gc_i18n_namespaceObject._x)('文档', 'noun')
    };
  }, []);
  const styles = useEditorStyles();

  const openSidebarPanel = () => openGeneralSidebar(hasBlockSelected ? 'edit-post/block' : 'edit-post/document'); // Inserter and Sidebars are mutually exclusive


  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (sidebarIsOpened && !isHugeViewport) {
      setIsInserterOpened(false);
    }
  }, [sidebarIsOpened, isHugeViewport]);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (isInserterOpened && !isHugeViewport) {
      closeGeneralSidebar();
    }
  }, [isInserterOpened, isHugeViewport]); // Local state for save panel.
  // Note 'truthy' callback implies an open panel.

  const [entitiesSavedStatesCallback, setEntitiesSavedStatesCallback] = (0,external_gc_element_namespaceObject.useState)(false);
  const closeEntitiesSavedStates = (0,external_gc_element_namespaceObject.useCallback)(arg => {
    if (typeof entitiesSavedStatesCallback === 'function') {
      entitiesSavedStatesCallback(arg);
    }

    setEntitiesSavedStatesCallback(false);
  }, [entitiesSavedStatesCallback]);
  const className = classnames_default()('edit-post-layout', 'is-mode-' + mode, {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar,
    'has-metaboxes': hasActiveMetaboxes,
    'show-icon-labels': showIconLabels,
    'is-distraction-free': isDistractionFree && isLargeViewport,
    'is-entity-save-view-open': !!entitiesSavedStatesCallback
  });
  const secondarySidebarLabel = isListViewOpened ? (0,external_gc_i18n_namespaceObject.__)('文档概述') : (0,external_gc_i18n_namespaceObject.__)('区块库');

  const secondarySidebar = () => {
    if (mode === 'visual' && isInserterOpened) {
      return (0,external_gc_element_namespaceObject.createElement)(InserterSidebar, null);
    }

    if (mode === 'visual' && isListViewOpened) {
      return (0,external_gc_element_namespaceObject.createElement)(ListViewSidebar, null);
    }

    return null;
  };

  function onPluginAreaError(name) {
    createErrorNotice((0,external_gc_i18n_namespaceObject.sprintf)(
    /* translators: %s: plugin name */
    (0,external_gc_i18n_namespaceObject.__)('插件“%s”遇到了一个错误，且无法被渲染。'), name));
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(fullscreen_mode, {
    isActive: isFullscreenActive
  }), (0,external_gc_element_namespaceObject.createElement)(browser_url, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.UnsavedChangesWarning, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.AutosaveMonitor, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.LocalAutosaveMonitor, null), (0,external_gc_element_namespaceObject.createElement)(keyboard_shortcuts, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.EditorKeyboardShortcutsRegister, null), (0,external_gc_element_namespaceObject.createElement)(settings_sidebar, null), (0,external_gc_element_namespaceObject.createElement)(interface_skeleton, {
    isDistractionFree: isDistractionFree && isLargeViewport,
    className: className,
    labels: { ...interfaceLabels,
      secondarySidebar: secondarySidebarLabel
    },
    header: (0,external_gc_element_namespaceObject.createElement)(header, {
      setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
    }),
    editorNotices: (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.EditorNotices, null),
    secondarySidebar: secondarySidebar(),
    sidebar: (!isMobileViewport || sidebarIsOpened) && (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, !isMobileViewport && !sidebarIsOpened && (0,external_gc_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__toggle-sidebar-panel"
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
      variant: "secondary",
      className: "edit-post-layout__toggle-sidebar-panel-button",
      onClick: openSidebarPanel,
      "aria-expanded": false
    }, hasBlockSelected ? (0,external_gc_i18n_namespaceObject.__)('开启区块设置') : (0,external_gc_i18n_namespaceObject.__)('开启文档设置'))), (0,external_gc_element_namespaceObject.createElement)(complementary_area.Slot, {
      scope: "core/edit-post"
    })),
    notices: (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.EditorSnackbars, null),
    content: (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, !isDistractionFree && (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.EditorNotices, null), (mode === 'text' || !isRichEditingEnabled) && (0,external_gc_element_namespaceObject.createElement)(TextEditor, null), isRichEditingEnabled && mode === 'visual' && (0,external_gc_element_namespaceObject.createElement)(VisualEditor, {
      styles: styles
    }), !isDistractionFree && !isTemplateMode && (0,external_gc_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__metaboxes"
    }, (0,external_gc_element_namespaceObject.createElement)(MetaBoxes, {
      location: "normal"
    }), (0,external_gc_element_namespaceObject.createElement)(MetaBoxes, {
      location: "advanced"
    })), isMobileViewport && sidebarIsOpened && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ScrollLock, null)),
    footer: !isDistractionFree && !isMobileViewport && showBlockBreadcrumbs && isRichEditingEnabled && mode === 'visual' && (0,external_gc_element_namespaceObject.createElement)("div", {
      className: "edit-post-layout__footer"
    }, (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockBreadcrumb, {
      rootLabelText: documentLabel
    })),
    actions: (0,external_gc_element_namespaceObject.createElement)(ActionsPanel, {
      closeEntitiesSavedStates: closeEntitiesSavedStates,
      isEntitiesSavedStatesOpen: entitiesSavedStatesCallback,
      setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
    }),
    shortcuts: {
      previous: previousShortcut,
      next: nextShortcut
    }
  }), (0,external_gc_element_namespaceObject.createElement)(EditPostPreferencesModal, null), (0,external_gc_element_namespaceObject.createElement)(keyboard_shortcut_help_modal, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostSyncStatusModal, null), (0,external_gc_element_namespaceObject.createElement)(StartPageOptions, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Popover.Slot, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_plugins_namespaceObject.PluginArea, {
    onError: onPluginAreaError
  }));
}

/* harmony default export */ var components_layout = (Layout);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/editor-initialization/listener-hooks.js
/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */


/**
 * This listener hook monitors for block selection and triggers the appropriate
 * sidebar state.
 *
 * @param {number} postId The current post id.
 */

const useBlockSelectionListener = postId => {
  const {
    hasBlockSelection,
    isEditorSidebarOpened
  } = (0,external_gc_data_namespaceObject.useSelect)(select => ({
    hasBlockSelection: !!select(external_gc_blockEditor_namespaceObject.store).getBlockSelectionStart(),
    isEditorSidebarOpened: select(constants_STORE_NAME).isEditorSidebarOpened()
  }), [postId]);
  const {
    openGeneralSidebar
  } = (0,external_gc_data_namespaceObject.useDispatch)(constants_STORE_NAME);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (!isEditorSidebarOpened) {
      return;
    }

    if (hasBlockSelection) {
      openGeneralSidebar('edit-post/block');
    } else {
      openGeneralSidebar('edit-post/document');
    }
  }, [hasBlockSelection, isEditorSidebarOpened]);
};
/**
 * This listener hook monitors any change in permalink and updates the view
 * post link in the admin bar.
 *
 * @param {number} postId
 */

const useUpdatePostLinkListener = postId => {
  const {
    newPermalink
  } = (0,external_gc_data_namespaceObject.useSelect)(select => ({
    newPermalink: select(external_gc_editor_namespaceObject.store).getCurrentPost().link
  }), [postId]);
  const nodeToUpdate = (0,external_gc_element_namespaceObject.useRef)();
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    nodeToUpdate.current = document.querySelector(VIEW_AS_PREVIEW_LINK_SELECTOR) || document.querySelector(VIEW_AS_LINK_SELECTOR);
  }, [postId]);
  (0,external_gc_element_namespaceObject.useEffect)(() => {
    if (!newPermalink || !nodeToUpdate.current) {
      return;
    }

    nodeToUpdate.current.setAttribute('href', newPermalink);
  }, [newPermalink]);
};

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/editor-initialization/index.js
/**
 * Internal dependencies
 */

/**
 * Data component used for initializing the editor and re-initializes
 * when postId changes or on unmount.
 *
 * @param {number} postId The id of the post.
 * @return {null} This is a data component so does not render any ui.
 */

function EditorInitialization({
  postId
}) {
  useBlockSelectionListener(postId);
  useUpdatePostLinkListener(postId);
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/block-default.js


/**
 * GeChiUI dependencies
 */

const blockDefault = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z"
}));
/* harmony default export */ var block_default = (blockDefault);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/cog.js


/**
 * GeChiUI dependencies
 */

const cog = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_cog = (cog);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/desktop.js


/**
 * GeChiUI dependencies
 */

const desktop = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M20.5 16h-.7V8c0-1.1-.9-2-2-2H6.2c-1.1 0-2 .9-2 2v8h-.7c-.8 0-1.5.7-1.5 1.5h20c0-.8-.7-1.5-1.5-1.5zM5.7 8c0-.3.2-.5.5-.5h11.6c.3 0 .5.2.5.5v7.6H5.7V8z"
}));
/* harmony default export */ var library_desktop = (desktop);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/code.js


/**
 * GeChiUI dependencies
 */

const code = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z"
}));
/* harmony default export */ var library_code = (code);

;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/keyboard-close.js


/**
 * GeChiUI dependencies
 */

const keyboardClose = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M18,0 L2,0 C0.9,0 0.01,0.9 0.01,2 L0,12 C0,13.1 0.9,14 2,14 L18,14 C19.1,14 20,13.1 20,12 L20,2 C20,0.9 19.1,0 18,0 Z M18,12 L2,12 L2,2 L18,2 L18,12 Z M9,3 L11,3 L11,5 L9,5 L9,3 Z M9,6 L11,6 L11,8 L9,8 L9,6 Z M6,3 L8,3 L8,5 L6,5 L6,3 Z M6,6 L8,6 L8,8 L6,8 L6,6 Z M3,6 L5,6 L5,8 L3,8 L3,6 Z M3,3 L5,3 L5,5 L3,5 L3,3 Z M6,9 L14,9 L14,11 L6,11 L6,9 Z M12,6 L14,6 L14,8 L12,8 L12,6 Z M12,3 L14,3 L14,5 L12,5 L12,3 Z M15,6 L17,6 L17,8 L15,8 L15,6 Z M15,3 L17,3 L17,5 L15,5 L15,3 Z M10,20 L14,16 L6,16 L10,20 Z"
}));
/* harmony default export */ var keyboard_close = (keyboardClose);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/hooks/commands/use-common-commands.js
/**
 * GeChiUI dependencies
 */






/**
 * Internal dependencies
 */




function useCommonCommands() {
  const {
    openGeneralSidebar,
    closeGeneralSidebar,
    switchEditorMode,
    setIsListViewOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const {
    openModal
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);
  const {
    editorMode,
    activeSidebar,
    isListViewOpen
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getEditorMode,
      isListViewOpened
    } = select(store_store);
    return {
      activeSidebar: select(store).getActiveComplementaryArea(store_store.name),
      editorMode: getEditorMode(),
      isListViewOpen: isListViewOpened()
    };
  }, []);
  const {
    toggle
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_preferences_namespaceObject.store);
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/open-settings-sidebar',
    label: (0,external_gc_i18n_namespaceObject.__)('切换设置侧边栏'),
    icon: (0,external_gc_i18n_namespaceObject.isRTL)() ? drawer_left : drawer_right,
    callback: ({
      close
    }) => {
      close();

      if (activeSidebar === 'edit-post/document') {
        closeGeneralSidebar();
      } else {
        openGeneralSidebar('edit-post/document');
      }
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/open-block-inspector',
    label: (0,external_gc_i18n_namespaceObject.__)('切换块检查器'),
    icon: block_default,
    callback: ({
      close
    }) => {
      close();

      if (activeSidebar === 'edit-post/block') {
        closeGeneralSidebar();
      } else {
        openGeneralSidebar('edit-post/block');
      }
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/toggle-distraction-free',
    label: (0,external_gc_i18n_namespaceObject.__)('切换专注模式'),
    icon: library_cog,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'distractionFree');
      close();
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/toggle-spotlight-mode',
    label: (0,external_gc_i18n_namespaceObject.__)('切换聚光灯模式'),
    icon: library_cog,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'focusMode');
      close();
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/toggle-fullscreen-mode',
    label: (0,external_gc_i18n_namespaceObject.__)('切换全屏模式'),
    icon: library_desktop,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'fullscreenMode');
      close();
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/toggle-list-view',
    label: (0,external_gc_i18n_namespaceObject.__)('切换列表视图'),
    icon: list_view,
    callback: ({
      close
    }) => {
      setIsListViewOpened(!isListViewOpen);
      close();
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/toggle-top-toolbar',
    label: (0,external_gc_i18n_namespaceObject.__)('切换顶部工具栏'),
    icon: library_cog,
    callback: ({
      close
    }) => {
      toggle('core/edit-post', 'fixedToolbar');
      close();
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/toggle-code-editor',
    label: (0,external_gc_i18n_namespaceObject.__)('切换代码编辑器'),
    icon: library_code,
    callback: ({
      close
    }) => {
      switchEditorMode(editorMode === 'visual' ? 'text' : 'visual');
      close();
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/open-preferences',
    label: (0,external_gc_i18n_namespaceObject.__)('打开编辑器首选项'),
    icon: library_cog,
    callback: () => {
      openModal(PREFERENCES_MODAL_NAME);
    }
  });
  (0,external_gc_commands_namespaceObject.useCommand)({
    name: 'core/open-shortcut-help',
    label: (0,external_gc_i18n_namespaceObject.__)('打开键盘快捷键'),
    icon: keyboard_close,
    callback: () => {
      openModal(KEYBOARD_SHORTCUT_HELP_MODAL_NAME);
    }
  });
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/editor.js


/**
 * GeChiUI dependencies
 */










/**
 * Internal dependencies
 */






const {
  ExperimentalEditorProvider
} = unlock(external_gc_editor_namespaceObject.privateApis);
const {
  useCommands
} = unlock(external_gc_coreCommands_namespaceObject.privateApis);

function Editor({
  postId,
  postType,
  settings,
  initialEdits,
  ...props
}) {
  useCommands();
  useCommonCommands();
  const {
    hasFixedToolbar,
    focusMode,
    isDistractionFree,
    hasInlineToolbar,
    post,
    preferredStyleVariations,
    hiddenBlockTypes,
    blockTypes,
    keepCaretInsideBlock,
    isTemplateMode,
    template
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    var _getPostType$viewable;

    const {
      isFeatureActive,
      isEditingTemplate,
      getEditedPostTemplate,
      getHiddenBlockTypes
    } = select(store_store);
    const {
      getEntityRecord,
      getPostType,
      getEntityRecords,
      canUser
    } = select(external_gc_coreData_namespaceObject.store);
    const {
      getEditorSettings
    } = select(external_gc_editor_namespaceObject.store);
    const {
      getBlockTypes
    } = select(external_gc_blocks_namespaceObject.store);
    const isTemplate = ['gc_template', 'gc_template_part'].includes(postType); // Ideally the initializeEditor function should be called using the ID of the REST endpoint.
    // to avoid the special case.

    let postObject;

    if (isTemplate) {
      const posts = getEntityRecords('postType', postType, {
        gc_id: postId
      });
      postObject = posts?.[0];
    } else {
      postObject = getEntityRecord('postType', postType, postId);
    }

    const supportsTemplateMode = getEditorSettings().supportsTemplateMode;
    const isViewable = (_getPostType$viewable = getPostType(postType)?.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false;
    const canEditTemplate = canUser('create', 'templates');
    return {
      hasFixedToolbar: isFeatureActive('fixedToolbar'),
      focusMode: isFeatureActive('focusMode'),
      isDistractionFree: isFeatureActive('distractionFree'),
      hasInlineToolbar: isFeatureActive('inlineToolbar'),
      preferredStyleVariations: select(external_gc_preferences_namespaceObject.store).get('core/edit-post', 'preferredStyleVariations'),
      hiddenBlockTypes: getHiddenBlockTypes(),
      blockTypes: getBlockTypes(),
      keepCaretInsideBlock: isFeatureActive('keepCaretInsideBlock'),
      isTemplateMode: isEditingTemplate(),
      template: supportsTemplateMode && isViewable && canEditTemplate ? getEditedPostTemplate() : null,
      post: postObject
    };
  }, [postType, postId]);
  const {
    updatePreferredStyleVariations,
    setIsInserterOpened
  } = (0,external_gc_data_namespaceObject.useDispatch)(store_store);
  const editorSettings = (0,external_gc_element_namespaceObject.useMemo)(() => {
    const result = { ...settings,
      __experimentalPreferredStyleVariations: {
        value: preferredStyleVariations,
        onChange: updatePreferredStyleVariations
      },
      hasFixedToolbar,
      focusMode,
      isDistractionFree,
      hasInlineToolbar,
      // This is marked as experimental to give time for the quick inserter to mature.
      __experimentalSetIsInserterOpened: setIsInserterOpened,
      keepCaretInsideBlock,
      // Keep a reference of the `allowedBlockTypes` from the server to handle use cases
      // where we need to differentiate if a block is disabled by the user or some plugin.
      defaultAllowedBlockTypes: settings.allowedBlockTypes
    }; // Omit hidden block types if exists and non-empty.

    if (hiddenBlockTypes.length > 0) {
      // Defer to passed setting for `allowedBlockTypes` if provided as
      // anything other than `true` (where `true` is equivalent to allow
      // all block types).
      const defaultAllowedBlockTypes = true === settings.allowedBlockTypes ? blockTypes.map(({
        name
      }) => name) : settings.allowedBlockTypes || [];
      result.allowedBlockTypes = defaultAllowedBlockTypes.filter(type => !hiddenBlockTypes.includes(type));
    }

    return result;
  }, [settings, hasFixedToolbar, hasInlineToolbar, focusMode, isDistractionFree, hiddenBlockTypes, blockTypes, preferredStyleVariations, setIsInserterOpened, updatePreferredStyleVariations, keepCaretInsideBlock]);

  if (!post) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_keyboardShortcuts_namespaceObject.ShortcutProvider, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.SlotFillProvider, null, (0,external_gc_element_namespaceObject.createElement)(ExperimentalEditorProvider, {
    settings: editorSettings,
    post: post,
    initialEdits: initialEdits,
    useSubRegistry: false,
    __unstableTemplate: isTemplateMode ? template : undefined,
    ...props
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.ErrorBoundary, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_commands_namespaceObject.CommandMenu, null), (0,external_gc_element_namespaceObject.createElement)(EditorInitialization, {
    postId: postId
  }), (0,external_gc_element_namespaceObject.createElement)(components_layout, null)), (0,external_gc_element_namespaceObject.createElement)(external_gc_editor_namespaceObject.PostLockedModal, null))));
}

/* harmony default export */ var editor = (Editor);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js


/**
 * GeChiUI dependencies
 */




const isEverySelectedBlockAllowed = (selected, allowed) => selected.filter(id => !allowed.includes(id)).length === 0;
/**
 * Plugins may want to add an item to the menu either for every block
 * or only for the specific ones provided in the `allowedBlocks` component property.
 *
 * If there are multiple blocks selected the item will be rendered if every block
 * is of one allowed type (not necessarily the same).
 *
 * @param {string[]} selectedBlocks Array containing the names of the blocks selected
 * @param {string[]} allowedBlocks  Array containing the names of the blocks allowed
 * @return {boolean} Whether the item will be rendered or not.
 */


const shouldRenderItem = (selectedBlocks, allowedBlocks) => !Array.isArray(allowedBlocks) || isEverySelectedBlockAllowed(selectedBlocks, allowedBlocks);
/**
 * Renders a new item in the block settings menu.
 *
 * @param {Object}                props                 Component props.
 * @param {Array}                 [props.allowedBlocks] An array containing a list of block names for which the item should be shown. If not present, it'll be rendered for any block. If multiple blocks are selected, it'll be shown if and only if all of them are in the allowed list.
 * @param {GCBlockTypeIconRender} [props.icon]          The [Dashicon](https://developer.gechiui.com/resource/dashicons/) icon slug string, or an SVG GC element.
 * @param {string}                props.label           The menu item text.
 * @param {Function}              props.onClick         Callback function to be executed when the user click the menu item.
 * @param {boolean}               [props.small]         Whether to render the label or not.
 * @param {string}                [props.role]          The ARIA role for the menu item.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = gc.i18n.__;
 * var PluginBlockSettingsMenuItem = gc.editPost.PluginBlockSettingsMenuItem;
 *
 * function doOnClick(){
 * 	// To be called when the user clicks the menu item.
 * }
 *
 * function MyPluginBlockSettingsMenuItem() {
 * 	return gc.element.createElement(
 * 		PluginBlockSettingsMenuItem,
 * 		{
 * 			allowedBlocks: [ 'core/paragraph' ],
 * 			icon: 'dashicon-name',
 * 			label: __( '菜单项文本' ),
 * 			onClick: doOnClick,
 * 		}
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@gechiui/i18n';
 * import { PluginBlockSettingsMenuItem } from '@gechiui/edit-post';
 *
 * const doOnClick = ( ) => {
 *     // To be called when the user clicks the menu item.
 * };
 *
 * const MyPluginBlockSettingsMenuItem = () => (
 *     <PluginBlockSettingsMenuItem
 * 		allowedBlocks={ [ 'core/paragraph' ] }
 * 		icon='dashicon-name'
 * 		label={ __( '菜单项文本' ) }
 * 		onClick={ doOnClick } />
 * );
 * ```
 *
 * @return {GCComponent} The component to be rendered.
 */


const PluginBlockSettingsMenuItem = ({
  allowedBlocks,
  icon,
  label,
  onClick,
  small,
  role
}) => (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockSettingsMenuControls, null, ({
  selectedBlocks,
  onClose
}) => {
  if (!shouldRenderItem(selectedBlocks, allowedBlocks)) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    onClick: (0,external_gc_compose_namespaceObject.compose)(onClick, onClose),
    icon: icon,
    label: small ? label : undefined,
    role: role
  }, !small && label);
});

/* harmony default export */ var plugin_block_settings_menu_item = (PluginBlockSettingsMenuItem);

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/plugin-more-menu-item/index.js
/**
 * GeChiUI dependencies
 */




/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down, and can be used to as a button or link depending on the props provided.
 * The text within the component appears as the menu item label.
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.href]                          When `href` is provided then the menu item is represented as an anchor rather than button. It corresponds to the `href` attribute of the anchor.
 * @param {GCBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.gechiui.com/resource/dashicons/) icon slug string, or an SVG GC element, to be rendered to the left of the menu item label.
 * @param {Function}              [props.onClick=noop]                  The callback function to be executed when the user clicks the menu item.
 * @param {...*}                  [props.other]                         Any additional props are passed through to the underlying [MenuItem](https://github.com/GeChiUI/gutenberg/tree/HEAD/packages/components/src/menu-item/README.md) component.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = gc.i18n.__;
 * var PluginMoreMenuItem = gc.editPost.PluginMoreMenuItem;
 * var moreIcon = gc.element.createElement( 'svg' ); //... svg element.
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * function MyButtonMoreMenuItem() {
 * 	return gc.element.createElement(
 * 		PluginMoreMenuItem,
 * 		{
 * 			icon: moreIcon,
 * 			onClick: onButtonClick,
 * 		},
 * 		__( '我的按钮标题' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@gechiui/i18n';
 * import { PluginMoreMenuItem } from '@gechiui/edit-post';
 * import { more } from '@gechiui/icons';
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * const MyButtonMoreMenuItem = () => (
 * 	<PluginMoreMenuItem
 * 		icon={ more }
 * 		onClick={ onButtonClick }
 * 	>
 * 		{ __( '我的按钮标题' ) }
 * 	</PluginMoreMenuItem>
 * );
 * ```
 *
 * @return {GCComponent} The component to be rendered.
 */

/* harmony default export */ var plugin_more_menu_item = ((0,external_gc_compose_namespaceObject.compose)((0,external_gc_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  var _ownProps$as;

  return {
    as: (_ownProps$as = ownProps.as) !== null && _ownProps$as !== void 0 ? _ownProps$as : external_gc_components_namespaceObject.MenuItem,
    icon: ownProps.icon || context.icon,
    name: 'core/edit-post/plugin-more-menu'
  };
}))(action_item));

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js


/**
 * GeChiUI dependencies
 */

/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down,
 * and can be used to activate the corresponding `PluginSidebar` component.
 * The text within the component appears as the menu item label.
 *
 * @param {Object}                props                                 Component props.
 * @param {string}                props.target                          A string identifying the target sidebar you wish to be activated by this menu item. Must be the same as the `name` prop you have given to that sidebar.
 * @param {GCBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.gechiui.com/resource/dashicons/) icon slug string, or an SVG GC element, to be rendered to the left of the menu item label.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = gc.i18n.__;
 * var PluginSidebarMoreMenuItem = gc.editPost.PluginSidebarMoreMenuItem;
 * var moreIcon = gc.element.createElement( 'svg' ); //... svg element.
 *
 * function MySidebarMoreMenuItem() {
 * 	return gc.element.createElement(
 * 		PluginSidebarMoreMenuItem,
 * 		{
 * 			target: 'my-sidebar',
 * 			icon: moreIcon,
 * 		},
 * 		__( '我的侧边栏标题' )
 * 	)
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@gechiui/i18n';
 * import { PluginSidebarMoreMenuItem } from '@gechiui/edit-post';
 * import { more } from '@gechiui/icons';
 *
 * const MySidebarMoreMenuItem = () => (
 * 	<PluginSidebarMoreMenuItem
 * 		target="my-sidebar"
 * 		icon={ more }
 * 	>
 * 		{ __( '我的侧边栏标题' ) }
 * 	</PluginSidebarMoreMenuItem>
 * );
 * ```
 *
 * @return {GCComponent} The component to be rendered.
 */

function PluginSidebarMoreMenuItem(props) {
  return (0,external_gc_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem // Menu item is marked with unstable prop for backward compatibility.
  // @see https://github.com/GeChiUI/gutenberg/issues/14457
  , {
    __unstableExplicitMenuItem: true,
    scope: "core/edit-post",
    ...props
  });
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/edit-post/build-module/index.js


/**
 * GeChiUI dependencies
 */








/**
 * Internal dependencies
 */





/**
 * Initializes and returns an instance of Editor.
 *
 * @param {string}  id           Unique identifier for editor instance.
 * @param {string}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */

function initializeEditor(id, postType, postId, settings, initialEdits) {
  const target = document.getElementById(id);
  const root = (0,external_gc_element_namespaceObject.createRoot)(target);
  (0,external_gc_data_namespaceObject.dispatch)(external_gc_preferences_namespaceObject.store).setDefaults('core/edit-post', {
    editorMode: 'visual',
    fixedToolbar: false,
    fullscreenMode: true,
    hiddenBlockTypes: [],
    inactivePanels: [],
    isPublishSidebarEnabled: true,
    openPanels: ['post-status'],
    preferredStyleVariations: {},
    showBlockBreadcrumbs: true,
    showIconLabels: false,
    showListViewByDefault: false,
    themeStyles: true
  });

  (0,external_gc_data_namespaceObject.dispatch)(external_gc_blocks_namespaceObject.store).__experimentalReapplyBlockTypeFilters(); // Check if the block list view should be open by default.
  // If `distractionFree` mode is enabled, the block list view should not be open.


  if ((0,external_gc_data_namespaceObject.select)(store_store).isFeatureActive('showListViewByDefault') && !(0,external_gc_data_namespaceObject.select)(store_store).isFeatureActive('distractionFree')) {
    (0,external_gc_data_namespaceObject.dispatch)(store_store).setIsListViewOpened(true);
  }

  (0,external_gc_blockLibrary_namespaceObject.registerCoreBlocks)();
  (0,external_gc_widgets_namespaceObject.registerLegacyWidgetBlock)({
    inserter: false
  });
  (0,external_gc_widgets_namespaceObject.registerWidgetGroupBlock)({
    inserter: false
  });

  if (false) {}
  /*
   * Prevent adding template part in the post editor.
   * Only add the filter when the post editor is initialized, not imported.
   * Also only add the filter(s) after registerCoreBlocks()
   * so that common filters in the block library are not overwritten.
   */


  (0,external_gc_hooks_namespaceObject.addFilter)('blockEditor.__unstableCanInsertBlockType', 'removeTemplatePartsFromInserter', (canInsert, blockType) => {
    if (!(0,external_gc_data_namespaceObject.select)(store_store).isEditingTemplate() && blockType.name === 'core/template-part') {
      return false;
    }

    return canInsert;
  });
  /*
   * Prevent adding post content block (except in query block) in the post editor.
   * Only add the filter when the post editor is initialized, not imported.
   * Also only add the filter(s) after registerCoreBlocks()
   * so that common filters in the block library are not overwritten.
   */

  (0,external_gc_hooks_namespaceObject.addFilter)('blockEditor.__unstableCanInsertBlockType', 'removePostContentFromInserter', (canInsert, blockType, rootClientId, {
    getBlockParentsByBlockName
  }) => {
    if (!(0,external_gc_data_namespaceObject.select)(store_store).isEditingTemplate() && blockType.name === 'core/post-content') {
      return getBlockParentsByBlockName(rootClientId, 'core/query').length > 0;
    }

    return canInsert;
  }); // Show a console log warning if the browser is not in Standards rendering mode.

  const documentMode = document.compatMode === 'CSS1Compat' ? 'Standards' : 'Quirks';

  if (documentMode !== 'Standards') {
    // eslint-disable-next-line no-console
    console.warn("Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins.");
  } // This is a temporary fix for a couple of issues specific to Webkit on iOS.
  // Without this hack the browser scrolls the mobile toolbar off-screen.
  // Once supported in Safari we can replace this in favor of preventScroll.
  // For details see issue #18632 and PR #18686
  // Specifically, we scroll `interface-interface-skeleton__body` to enable a fixed top toolbar.
  // But Mobile Safari forces the `html` element to scroll upwards, hiding the toolbar.


  const isIphone = window.navigator.userAgent.indexOf('iPhone') !== -1;

  if (isIphone) {
    window.addEventListener('scroll', event => {
      const editorScrollContainer = document.getElementsByClassName('interface-interface-skeleton__body')[0];

      if (event.target === document) {
        // Scroll element into view by scrolling the editor container by the same amount
        // that Mobile Safari tried to scroll the html element upwards.
        if (window.scrollY > 100) {
          editorScrollContainer.scrollTop = editorScrollContainer.scrollTop + window.scrollY;
        } // Undo unwanted scroll on html element, but only in the visual editor.


        if (document.getElementsByClassName('is-mode-visual')[0]) {
          window.scrollTo(0, 0);
        }
      }
    });
  } // Prevent the default browser action for files dropped outside of dropzones.


  window.addEventListener('dragover', e => e.preventDefault(), false);
  window.addEventListener('drop', e => e.preventDefault(), false);
  root.render((0,external_gc_element_namespaceObject.createElement)(editor, {
    settings: settings,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }));
  return root;
}
/**
 * Used to reinitialize the editor after an error. Now it's a deprecated noop function.
 */

function reinitializeEditor() {
  external_gc_deprecated_default()('gc.editPost.reinitializeEditor', {
    since: '6.2',
    version: '6.3'
  });
}












}();
(window.gc = window.gc || {}).editPost = __webpack_exports__;
/******/ })()
;