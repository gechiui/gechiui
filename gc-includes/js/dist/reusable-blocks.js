this["gc"] = this["gc"] || {}; this["gc"]["reusableBlocks"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "nPLi");
/******/ })
/************************************************************************/
/******/ ({

/***/ "Gz8V":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["primitives"]; }());

/***/ }),

/***/ "IgLd":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["data"]; }());

/***/ }),

/***/ "NxuN":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["coreData"]; }());

/***/ }),

/***/ "W2Kb":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["notices"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "ewfG":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["element"]; }());

/***/ }),

/***/ "jd0n":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["components"]; }());

/***/ }),

/***/ "n68F":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["blocks"]; }());

/***/ }),

/***/ "nLrk":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["blockEditor"]; }());

/***/ }),

/***/ "nPLi":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });
__webpack_require__.d(__webpack_exports__, "ReusableBlocksMenuItems", function() { return /* reexport */ reusable_blocks_menu_items; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/reusable-blocks/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "__experimentalConvertBlockToStatic", function() { return __experimentalConvertBlockToStatic; });
__webpack_require__.d(actions_namespaceObject, "__experimentalConvertBlocksToReusable", function() { return __experimentalConvertBlocksToReusable; });
__webpack_require__.d(actions_namespaceObject, "__experimentalDeleteReusableBlock", function() { return __experimentalDeleteReusableBlock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalSetEditingReusableBlock", function() { return __experimentalSetEditingReusableBlock; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/reusable-blocks/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "__experimentalIsEditingReusableBlock", function() { return __experimentalIsEditingReusableBlock; });

// EXTERNAL MODULE: external ["gc","data"]
var external_gc_data_ = __webpack_require__("IgLd");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["gc","blockEditor"]
var external_gc_blockEditor_ = __webpack_require__("nLrk");

// EXTERNAL MODULE: external ["gc","blocks"]
var external_gc_blocks_ = __webpack_require__("n68F");

// EXTERNAL MODULE: external ["gc","i18n"]
var external_gc_i18n_ = __webpack_require__("z4sU");

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/actions.js
/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */




/**
 * Returns a generator converting a reusable block into a static block.
 *
 * @param {string} clientId The client ID of the block to attach.
 */

const __experimentalConvertBlockToStatic = clientId => _ref => {
  let {
    registry
  } = _ref;
  const oldBlock = registry.select(external_gc_blockEditor_["store"]).getBlock(clientId);
  const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'gc_block', oldBlock.attributes.ref);
  const newBlocks = Object(external_gc_blocks_["parse"])(Object(external_lodash_["isFunction"])(reusableBlock.content) ? reusableBlock.content(reusableBlock) : reusableBlock.content);
  registry.dispatch(external_gc_blockEditor_["store"]).replaceBlocks(oldBlock.clientId, newBlocks);
};
/**
 * Returns a generator converting one or more static blocks into a reusable block.
 *
 * @param {string[]} clientIds The client IDs of the block to detach.
 * @param {string}   title     Reusable block title.
 */

const __experimentalConvertBlocksToReusable = (clientIds, title) => async _ref2 => {
  let {
    registry,
    dispatch
  } = _ref2;
  const reusableBlock = {
    title: title || Object(external_gc_i18n_["__"])('未命名可重用区块'),
    content: Object(external_gc_blocks_["serialize"])(registry.select(external_gc_blockEditor_["store"]).getBlocksByClientId(clientIds)),
    status: 'publish'
  };
  const updatedRecord = await registry.dispatch('core').saveEntityRecord('postType', 'gc_block', reusableBlock);
  const newBlock = Object(external_gc_blocks_["createBlock"])('core/block', {
    ref: updatedRecord.id
  });
  registry.dispatch(external_gc_blockEditor_["store"]).replaceBlocks(clientIds, newBlock);

  dispatch.__experimentalSetEditingReusableBlock(newBlock.clientId, true);
};
/**
 * Returns a generator deleting a reusable block.
 *
 * @param {string} id The ID of the reusable block to delete.
 */

const __experimentalDeleteReusableBlock = id => async _ref3 => {
  let {
    registry
  } = _ref3;
  const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'gc_block', id); // Don't allow a reusable block with a temporary ID to be deleted

  if (!reusableBlock) {
    return;
  } // Remove any other blocks that reference this reusable block


  const allBlocks = registry.select(external_gc_blockEditor_["store"]).getBlocks();
  const associatedBlocks = allBlocks.filter(block => Object(external_gc_blocks_["isReusableBlock"])(block) && block.attributes.ref === id);
  const associatedBlockClientIds = associatedBlocks.map(block => block.clientId); // Remove the parsed block.

  if (associatedBlockClientIds.length) {
    registry.dispatch(external_gc_blockEditor_["store"]).removeBlocks(associatedBlockClientIds);
  }

  await registry.dispatch('core').deleteEntityRecord('postType', 'gc_block', id);
};
/**
 * Returns an action descriptor for SET_EDITING_REUSABLE_BLOCK action.
 *
 * @param {string}  clientId  The clientID of the reusable block to target.
 * @param {boolean} isEditing Whether the block should be in editing state.
 * @return {Object} Action descriptor.
 */

function __experimentalSetEditingReusableBlock(clientId, isEditing) {
  return {
    type: 'SET_EDITING_REUSABLE_BLOCK',
    clientId,
    isEditing
  };
}

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/reducer.js
/**
 * GeChiUI dependencies
 */

function isEditingReusableBlock() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  if ((action === null || action === void 0 ? void 0 : action.type) === 'SET_EDITING_REUSABLE_BLOCK') {
    return { ...state,
      [action.clientId]: action.isEditing
    };
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_gc_data_["combineReducers"])({
  isEditingReusableBlock
}));

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/selectors.js
/**
 * Returns true if reusable block is in the editing state.
 *
 * @param {Object} state    Global application state.
 * @param {number} clientId the clientID of the block.
 * @return {boolean} Whether the reusable block is in the editing state.
 */
function __experimentalIsEditingReusableBlock(state, clientId) {
  return state.isEditingReusableBlock[clientId];
}

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/index.js
/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */




const STORE_NAME = 'core/reusable-blocks';
/**
 * Store definition for the reusable blocks namespace.
 *
 * @see https://github.com/GeChiUI/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_gc_data_["createReduxStore"])(STORE_NAME, {
  actions: actions_namespaceObject,
  reducer: reducer,
  selectors: selectors_namespaceObject,
  __experimentalUseThunks: true
});
Object(external_gc_data_["register"])(store);

// EXTERNAL MODULE: external ["gc","element"]
var external_gc_element_ = __webpack_require__("ewfG");

// EXTERNAL MODULE: external ["gc","components"]
var external_gc_components_ = __webpack_require__("jd0n");

// EXTERNAL MODULE: external ["gc","primitives"]
var external_gc_primitives_ = __webpack_require__("Gz8V");

// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/reusable-block.js


/**
 * GeChiUI dependencies
 */

const reusable_block_reusableBlock = Object(external_gc_element_["createElement"])(external_gc_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_gc_element_["createElement"])(external_gc_primitives_["Path"], {
  d: "M7 7.2h8.2L13.5 9l1.1 1.1 3.6-3.6-3.5-4-1.1 1 1.9 2.3H7c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.2-.5zm13.8 4V11h-1.5v.3c0 1.1 0 3.5-1 4.5-.3.3-.7.5-1.3.5H8.8l1.7-1.7-1.1-1.1L5.9 17l3.5 4 1.1-1-1.9-2.3H17c.9 0 1.7-.3 2.3-.9 1.5-1.4 1.5-4.2 1.5-5.6z"
}));
/* harmony default export */ var reusable_block = (reusable_block_reusableBlock);

// EXTERNAL MODULE: external ["gc","notices"]
var external_gc_notices_ = __webpack_require__("W2Kb");

// EXTERNAL MODULE: external ["gc","coreData"]
var external_gc_coreData_ = __webpack_require__("NxuN");

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-block-convert-button.js


/**
 * GeChiUI dependencies
 */









/**
 * Internal dependencies
 */


/**
 * Menu control to convert block(s) to reusable block.
 *
 * @param {Object}   props              Component props.
 * @param {string[]} props.clientIds    Client ids of selected blocks.
 * @param {string}   props.rootClientId ID of the currently selected top-level block.
 * @return {import('@gechiui/element').GCComponent} The menu control or null.
 */

function ReusableBlockConvertButton(_ref) {
  let {
    clientIds,
    rootClientId
  } = _ref;
  const [isModalOpen, setIsModalOpen] = Object(external_gc_element_["useState"])(false);
  const [title, setTitle] = Object(external_gc_element_["useState"])('');
  const canConvert = Object(external_gc_data_["useSelect"])(select => {
    var _getBlocksByClientId;

    const {
      canUser
    } = select(external_gc_coreData_["store"]);
    const {
      getBlocksByClientId,
      canInsertBlockType
    } = select(external_gc_blockEditor_["store"]);
    const blocks = (_getBlocksByClientId = getBlocksByClientId(clientIds)) !== null && _getBlocksByClientId !== void 0 ? _getBlocksByClientId : [];
    const isReusable = blocks.length === 1 && blocks[0] && Object(external_gc_blocks_["isReusableBlock"])(blocks[0]) && !!select(external_gc_coreData_["store"]).getEntityRecord('postType', 'gc_block', blocks[0].attributes.ref);

    const _canConvert = // Hide when this is already a reusable block.
    !isReusable && // Hide when reusable blocks are disabled.
    canInsertBlockType('core/block', rootClientId) && blocks.every(block => // Guard against the case where a regular block has *just* been converted.
    !!block && // Hide on invalid blocks.
    block.isValid && // Hide when block doesn't support being made reusable.
    Object(external_gc_blocks_["hasBlockSupport"])(block.name, 'reusable', true)) && // Hide when current doesn't have permission to do that.
    !!canUser('create', 'blocks');

    return _canConvert;
  }, [clientIds]);
  const {
    __experimentalConvertBlocksToReusable: convertBlocksToReusable
  } = Object(external_gc_data_["useDispatch"])(store);
  const {
    createSuccessNotice,
    createErrorNotice
  } = Object(external_gc_data_["useDispatch"])(external_gc_notices_["store"]);
  const onConvert = Object(external_gc_element_["useCallback"])(async function (reusableBlockTitle) {
    try {
      await convertBlocksToReusable(clientIds, reusableBlockTitle);
      createSuccessNotice(Object(external_gc_i18n_["__"])('可重用区块已创建。'), {
        type: 'snackbar'
      });
    } catch (error) {
      createErrorNotice(error.message, {
        type: 'snackbar'
      });
    }
  }, [clientIds]);

  if (!canConvert) {
    return null;
  }

  return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockSettingsMenuControls"], null, _ref2 => {
    let {
      onClose
    } = _ref2;
    return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(external_gc_components_["MenuItem"], {
      icon: reusable_block,
      onClick: () => {
        setIsModalOpen(true);
      }
    }, Object(external_gc_i18n_["__"])('添加至可重用区块')), isModalOpen && Object(external_gc_element_["createElement"])(external_gc_components_["Modal"], {
      title: Object(external_gc_i18n_["__"])('创建可重用区块'),
      closeLabel: Object(external_gc_i18n_["__"])('关闭'),
      onRequestClose: () => {
        setIsModalOpen(false);
        setTitle('');
      },
      overlayClassName: "reusable-blocks-menu-items__convert-modal"
    }, Object(external_gc_element_["createElement"])("form", {
      onSubmit: event => {
        event.preventDefault();
        onConvert(title);
        setIsModalOpen(false);
        setTitle('');
        onClose();
      }
    }, Object(external_gc_element_["createElement"])(external_gc_components_["TextControl"], {
      label: Object(external_gc_i18n_["__"])('显示名称'),
      value: title,
      onChange: setTitle
    }), Object(external_gc_element_["createElement"])(external_gc_components_["Flex"], {
      className: "reusable-blocks-menu-items__convert-modal-actions",
      justify: "flex-end"
    }, Object(external_gc_element_["createElement"])(external_gc_components_["FlexItem"], null, Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
      variant: "secondary",
      onClick: () => {
        setIsModalOpen(false);
        setTitle('');
      }
    }, Object(external_gc_i18n_["__"])('取消'))), Object(external_gc_element_["createElement"])(external_gc_components_["FlexItem"], null, Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
      variant: "primary",
      type: "submit"
    }, Object(external_gc_i18n_["__"])('保存')))))));
  });
}

// EXTERNAL MODULE: external ["gc","url"]
var external_gc_url_ = __webpack_require__("zP/e");

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-blocks-manage-button.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */



function ReusableBlocksManageButton(_ref) {
  let {
    clientId
  } = _ref;
  const {
    isVisible
  } = Object(external_gc_data_["useSelect"])(select => {
    const {
      getBlock
    } = select(external_gc_blockEditor_["store"]);
    const {
      canUser
    } = select(external_gc_coreData_["store"]);
    const reusableBlock = getBlock(clientId);
    return {
      isVisible: !!reusableBlock && Object(external_gc_blocks_["isReusableBlock"])(reusableBlock) && !!canUser('update', 'blocks', reusableBlock.attributes.ref)
    };
  }, [clientId]);
  const {
    __experimentalConvertBlockToStatic: convertBlockToStatic
  } = Object(external_gc_data_["useDispatch"])(store);

  if (!isVisible) {
    return null;
  }

  return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockSettingsMenuControls"], null, Object(external_gc_element_["createElement"])(external_gc_components_["MenuItem"], {
    href: Object(external_gc_url_["addQueryArgs"])('edit.php', {
      post_type: 'gc_block'
    })
  }, Object(external_gc_i18n_["__"])('管理可重用区块')), Object(external_gc_element_["createElement"])(external_gc_components_["MenuItem"], {
    onClick: () => convertBlockToStatic(clientId)
  }, Object(external_gc_i18n_["__"])('转换为常规区块')));
}

/* harmony default export */ var reusable_blocks_manage_button = (ReusableBlocksManageButton);

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/reusable-blocks-menu-items/index.js


/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */




function ReusableBlocksMenuItems(_ref) {
  let {
    clientIds,
    rootClientId
  } = _ref;
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(ReusableBlockConvertButton, {
    clientIds: clientIds,
    rootClientId: rootClientId
  }), clientIds.length === 1 && Object(external_gc_element_["createElement"])(reusable_blocks_manage_button, {
    clientId: clientIds[0]
  }));
}

/* harmony default export */ var reusable_blocks_menu_items = (Object(external_gc_data_["withSelect"])(select => {
  const {
    getSelectedBlockClientIds
  } = select(external_gc_blockEditor_["store"]);
  return {
    clientIds: getSelectedBlockClientIds()
  };
})(ReusableBlocksMenuItems));

// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/index.js


// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/index.js




/***/ }),

/***/ "z4sU":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["i18n"]; }());

/***/ }),

/***/ "zP/e":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["url"]; }());

/***/ })

/******/ });