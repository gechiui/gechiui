/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "ReusableBlocksMenuItems": function() { return /* reexport */ ReusableBlocksMenuItems; },
  "store": function() { return /* reexport */ store; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/reusable-blocks/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "__experimentalConvertBlockToStatic": function() { return __experimentalConvertBlockToStatic; },
  "__experimentalConvertBlocksToReusable": function() { return __experimentalConvertBlocksToReusable; },
  "__experimentalDeleteReusableBlock": function() { return __experimentalDeleteReusableBlock; },
  "__experimentalSetEditingReusableBlock": function() { return __experimentalSetEditingReusableBlock; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/reusable-blocks/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "__experimentalIsEditingReusableBlock": function() { return __experimentalIsEditingReusableBlock; }
});

;// CONCATENATED MODULE: external ["gc","data"]
var external_gc_data_namespaceObject = window["gc"]["data"];
;// CONCATENATED MODULE: external ["gc","blockEditor"]
var external_gc_blockEditor_namespaceObject = window["gc"]["blockEditor"];
;// CONCATENATED MODULE: external ["gc","blocks"]
var external_gc_blocks_namespaceObject = window["gc"]["blocks"];
;// CONCATENATED MODULE: external ["gc","i18n"]
var external_gc_i18n_namespaceObject = window["gc"]["i18n"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/actions.js
/**
 * GeChiUI dependencies
 */



/**
 * Returns a generator converting a reusable block into a static block.
 *
 * @param {string} clientId The client ID of the block to attach.
 */

const __experimentalConvertBlockToStatic = clientId => ({
  registry
}) => {
  const oldBlock = registry.select(external_gc_blockEditor_namespaceObject.store).getBlock(clientId);
  const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'gc_block', oldBlock.attributes.ref);
  const newBlocks = (0,external_gc_blocks_namespaceObject.parse)(typeof reusableBlock.content === 'function' ? reusableBlock.content(reusableBlock) : reusableBlock.content);
  registry.dispatch(external_gc_blockEditor_namespaceObject.store).replaceBlocks(oldBlock.clientId, newBlocks);
};
/**
 * Returns a generator converting one or more static blocks into a pattern.
 *
 * @param {string[]}             clientIds The client IDs of the block to detach.
 * @param {string}               title     Pattern title.
 * @param {undefined|'unsynced'} syncType  They way block is synced, current undefined (synced) and 'unsynced'.
 */

const __experimentalConvertBlocksToReusable = (clientIds, title, syncType) => async ({
  registry,
  dispatch
}) => {
  const meta = syncType === 'unsynced' ? {
    gc_pattern_sync_status: syncType
  } : undefined;
  const reusableBlock = {
    title: title || (0,external_gc_i18n_namespaceObject.__)('未命名的样板区块'),
    content: (0,external_gc_blocks_namespaceObject.serialize)(registry.select(external_gc_blockEditor_namespaceObject.store).getBlocksByClientId(clientIds)),
    status: 'publish',
    meta
  };
  const updatedRecord = await registry.dispatch('core').saveEntityRecord('postType', 'gc_block', reusableBlock);

  if (syncType === 'unsynced') {
    return;
  }

  const newBlock = (0,external_gc_blocks_namespaceObject.createBlock)('core/block', {
    ref: updatedRecord.id
  });
  registry.dispatch(external_gc_blockEditor_namespaceObject.store).replaceBlocks(clientIds, newBlock);

  dispatch.__experimentalSetEditingReusableBlock(newBlock.clientId, true);
};
/**
 * Returns a generator deleting a reusable block.
 *
 * @param {string} id The ID of the reusable block to delete.
 */

const __experimentalDeleteReusableBlock = id => async ({
  registry
}) => {
  const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'gc_block', id); // Don't allow a reusable block with a temporary ID to be deleted.

  if (!reusableBlock) {
    return;
  } // Remove any other blocks that reference this reusable block.


  const allBlocks = registry.select(external_gc_blockEditor_namespaceObject.store).getBlocks();
  const associatedBlocks = allBlocks.filter(block => (0,external_gc_blocks_namespaceObject.isReusableBlock)(block) && block.attributes.ref === id);
  const associatedBlockClientIds = associatedBlocks.map(block => block.clientId); // Remove the parsed block.

  if (associatedBlockClientIds.length) {
    registry.dispatch(external_gc_blockEditor_namespaceObject.store).removeBlocks(associatedBlockClientIds);
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

;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/reducer.js
/**
 * GeChiUI dependencies
 */

function isEditingReusableBlock(state = {}, action) {
  if (action?.type === 'SET_EDITING_REUSABLE_BLOCK') {
    return { ...state,
      [action.clientId]: action.isEditing
    };
  }

  return state;
}
/* harmony default export */ var reducer = ((0,external_gc_data_namespaceObject.combineReducers)({
  isEditingReusableBlock
}));

;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/selectors.js
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

;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/store/index.js
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

const store = (0,external_gc_data_namespaceObject.createReduxStore)(STORE_NAME, {
  actions: actions_namespaceObject,
  reducer: reducer,
  selectors: selectors_namespaceObject
});
(0,external_gc_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: external ["gc","element"]
var external_gc_element_namespaceObject = window["gc"]["element"];
;// CONCATENATED MODULE: external ["gc","components"]
var external_gc_components_namespaceObject = window["gc"]["components"];
;// CONCATENATED MODULE: external ["gc","primitives"]
var external_gc_primitives_namespaceObject = window["gc"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/symbol.js


/**
 * GeChiUI dependencies
 */

const symbol = (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_gc_element_namespaceObject.createElement)(external_gc_primitives_namespaceObject.Path, {
  d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z"
}));
/* harmony default export */ var library_symbol = (symbol);

;// CONCATENATED MODULE: external ["gc","notices"]
var external_gc_notices_namespaceObject = window["gc"]["notices"];
;// CONCATENATED MODULE: external ["gc","coreData"]
var external_gc_coreData_namespaceObject = window["gc"]["coreData"];
;// CONCATENATED MODULE: external ["gc","privateApis"]
var external_gc_privateApis_namespaceObject = window["gc"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/lock-unlock.js
/**
 * GeChiUI dependencies
 */

const {
  unlock
} = (0,external_gc_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next GeChiUI release.', '@gechiui/reusable-blocks');

;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-block-convert-button.js


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

function ReusableBlockConvertButton({
  clientIds,
  rootClientId
}) {
  const {
    useReusableBlocksRenameHint,
    ReusableBlocksRenameHint
  } = unlock(external_gc_blockEditor_namespaceObject.privateApis);
  const showRenameHint = useReusableBlocksRenameHint();
  const [syncType, setSyncType] = (0,external_gc_element_namespaceObject.useState)(undefined);
  const [isModalOpen, setIsModalOpen] = (0,external_gc_element_namespaceObject.useState)(false);
  const [title, setTitle] = (0,external_gc_element_namespaceObject.useState)('');
  const canConvert = (0,external_gc_data_namespaceObject.useSelect)(select => {
    var _getBlocksByClientId;

    const {
      canUser
    } = select(external_gc_coreData_namespaceObject.store);
    const {
      getBlocksByClientId,
      canInsertBlockType,
      getBlockRootClientId
    } = select(external_gc_blockEditor_namespaceObject.store);
    const rootId = rootClientId || (clientIds.length > 0 ? getBlockRootClientId(clientIds[0]) : undefined);
    const blocks = (_getBlocksByClientId = getBlocksByClientId(clientIds)) !== null && _getBlocksByClientId !== void 0 ? _getBlocksByClientId : [];
    const isReusable = blocks.length === 1 && blocks[0] && (0,external_gc_blocks_namespaceObject.isReusableBlock)(blocks[0]) && !!select(external_gc_coreData_namespaceObject.store).getEntityRecord('postType', 'gc_block', blocks[0].attributes.ref);

    const _canConvert = // Hide when this is already a reusable block.
    !isReusable && // Hide when reusable blocks are disabled.
    canInsertBlockType('core/block', rootId) && blocks.every(block => // Guard against the case where a regular block has *just* been converted.
    !!block && // Hide on invalid blocks.
    block.isValid && // Hide when block doesn't support being made reusable.
    (0,external_gc_blocks_namespaceObject.hasBlockSupport)(block.name, 'reusable', true)) && // Hide when current doesn't have permission to do that.
    !!canUser('create', 'blocks');

    return _canConvert;
  }, [clientIds, rootClientId]);
  const {
    __experimentalConvertBlocksToReusable: convertBlocksToReusable
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);
  const {
    createSuccessNotice,
    createErrorNotice
  } = (0,external_gc_data_namespaceObject.useDispatch)(external_gc_notices_namespaceObject.store);
  const onConvert = (0,external_gc_element_namespaceObject.useCallback)(async function (reusableBlockTitle) {
    try {
      await convertBlocksToReusable(clientIds, reusableBlockTitle, syncType);
      createSuccessNotice(!syncType ? (0,external_gc_i18n_namespaceObject.sprintf)( // translators: %s: the name the user has given to the pattern.
      (0,external_gc_i18n_namespaceObject.__)('已创建同步区块样板：%s'), reusableBlockTitle) : (0,external_gc_i18n_namespaceObject.sprintf)( // translators: %s: the name the user has given to the pattern.
      (0,external_gc_i18n_namespaceObject.__)('已创建未同步的区块样板：%s'), reusableBlockTitle), {
        type: 'snackbar',
        id: 'convert-to-reusable-block-success'
      });
    } catch (error) {
      createErrorNotice(error.message, {
        type: 'snackbar',
        id: 'convert-to-reusable-block-error'
      });
    }
  }, [convertBlocksToReusable, clientIds, syncType, createSuccessNotice, createErrorNotice]);

  if (!canConvert) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockSettingsMenuControls, null, ({
    onClose
  }) => (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    icon: library_symbol,
    onClick: () => setIsModalOpen(true)
  }, showRenameHint ? (0,external_gc_i18n_namespaceObject.__)('创建区块样板/可重复使用区块') : (0,external_gc_i18n_namespaceObject.__)('创建模板')), isModalOpen && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Modal, {
    title: (0,external_gc_i18n_namespaceObject.__)('创建模板'),
    onRequestClose: () => {
      setIsModalOpen(false);
      setTitle('');
    },
    overlayClassName: "reusable-blocks-menu-items__convert-modal"
  }, (0,external_gc_element_namespaceObject.createElement)("form", {
    onSubmit: event => {
      event.preventDefault();
      onConvert(title);
      setIsModalOpen(false);
      setTitle('');
      onClose();
    }
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalVStack, {
    spacing: "5"
  }, (0,external_gc_element_namespaceObject.createElement)(ReusableBlocksRenameHint, null), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_gc_i18n_namespaceObject.__)('名称'),
    value: title,
    onChange: setTitle,
    placeholder: (0,external_gc_i18n_namespaceObject.__)('我的样板')
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.ToggleControl, {
    label: (0,external_gc_i18n_namespaceObject.__)('已同步'),
    help: (0,external_gc_i18n_namespaceObject.__)('编辑图案将在使用的任何位置进行更新。'),
    checked: !syncType,
    onChange: () => {
      setSyncType(!syncType ? 'unsynced' : undefined);
    }
  }), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.__experimentalHStack, {
    justify: "right"
  }, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: () => {
      setIsModalOpen(false);
      setTitle('');
    }
  }, (0,external_gc_i18n_namespaceObject.__)('取消')), (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit"
  }, (0,external_gc_i18n_namespaceObject.__)('创建'))))))));
}

;// CONCATENATED MODULE: external ["gc","url"]
var external_gc_url_namespaceObject = window["gc"]["url"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-blocks-manage-button.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */



function ReusableBlocksManageButton({
  clientId
}) {
  const {
    canRemove,
    isVisible,
    innerBlockCount,
    managePatternsUrl
  } = (0,external_gc_data_namespaceObject.useSelect)(select => {
    const {
      getBlock,
      canRemoveBlock,
      getBlockCount,
      getSettings
    } = select(external_gc_blockEditor_namespaceObject.store);
    const {
      canUser
    } = select(external_gc_coreData_namespaceObject.store);
    const reusableBlock = getBlock(clientId);

    const isBlockTheme = getSettings().__unstableIsBlockBasedTheme;

    return {
      canRemove: canRemoveBlock(clientId),
      isVisible: !!reusableBlock && (0,external_gc_blocks_namespaceObject.isReusableBlock)(reusableBlock) && !!canUser('update', 'blocks', reusableBlock.attributes.ref),
      innerBlockCount: getBlockCount(clientId),
      // The site editor and templates both check whether the user
      // has edit_theme_options capabilities. We can leverage that here
      // and omit the manage patterns link if the user can't access it.
      managePatternsUrl: isBlockTheme && canUser('read', 'templates') ? (0,external_gc_url_namespaceObject.addQueryArgs)('site-editor.php', {
        path: '/patterns'
      }) : (0,external_gc_url_namespaceObject.addQueryArgs)('edit.php', {
        post_type: 'gc_block'
      })
    };
  }, [clientId]);
  const {
    __experimentalConvertBlockToStatic: convertBlockToStatic
  } = (0,external_gc_data_namespaceObject.useDispatch)(store);

  if (!isVisible) {
    return null;
  }

  return (0,external_gc_element_namespaceObject.createElement)(external_gc_blockEditor_namespaceObject.BlockSettingsMenuControls, null, (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    href: managePatternsUrl
  }, (0,external_gc_i18n_namespaceObject.__)('管理样板')), canRemove && (0,external_gc_element_namespaceObject.createElement)(external_gc_components_namespaceObject.MenuItem, {
    onClick: () => convertBlockToStatic(clientId)
  }, innerBlockCount > 1 ? (0,external_gc_i18n_namespaceObject.__)('拆分模板') : (0,external_gc_i18n_namespaceObject.__)('拆分模板')));
}

/* harmony default export */ var reusable_blocks_manage_button = (ReusableBlocksManageButton);

;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/reusable-blocks-menu-items/index.js


/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */



function ReusableBlocksMenuItems({
  rootClientId
}) {
  const clientIds = (0,external_gc_data_namespaceObject.useSelect)(select => select(external_gc_blockEditor_namespaceObject.store).getSelectedBlockClientIds(), []);
  return (0,external_gc_element_namespaceObject.createElement)(external_gc_element_namespaceObject.Fragment, null, (0,external_gc_element_namespaceObject.createElement)(ReusableBlockConvertButton, {
    clientIds: clientIds,
    rootClientId: rootClientId
  }), clientIds.length === 1 && (0,external_gc_element_namespaceObject.createElement)(reusable_blocks_manage_button, {
    clientId: clientIds[0]
  }));
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/components/index.js


;// CONCATENATED MODULE: ./node_modules/@gechiui/reusable-blocks/build-module/index.js



(window.gc = window.gc || {}).reusableBlocks = __webpack_exports__;
/******/ })()
;