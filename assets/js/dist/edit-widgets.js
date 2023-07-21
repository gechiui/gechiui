this["gc"] = this["gc"] || {}; this["gc"]["editWidgets"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "di29");
/******/ })
/************************************************************************/
/******/ ({

/***/ "0GdS":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const blockDefault = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (blockDefault);


/***/ }),

/***/ "2BQN":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const listView = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13.8 5.2H3v1.5h10.8V5.2zm-3.6 12v1.5H21v-1.5H10.2zm7.2-6H6.6v1.5h10.8v-1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (listView);


/***/ }),

/***/ "2YC4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "b", function() { return /* reexport */ complementary_area; });
__webpack_require__.d(__webpack_exports__, "c", function() { return /* reexport */ ComplementaryAreaMoreMenuItem; });
__webpack_require__.d(__webpack_exports__, "d", function() { return /* reexport */ fullscreen_mode; });
__webpack_require__.d(__webpack_exports__, "e", function() { return /* reexport */ interface_skeleton; });
__webpack_require__.d(__webpack_exports__, "h", function() { return /* reexport */ pinned_items; });
__webpack_require__.d(__webpack_exports__, "f", function() { return /* reexport */ MoreMenuDropdown; });
__webpack_require__.d(__webpack_exports__, "g", function() { return /* reexport */ MoreMenuFeatureToggle; });
__webpack_require__.d(__webpack_exports__, "a", function() { return /* reexport */ action_item; });
__webpack_require__.d(__webpack_exports__, "i", function() { return /* reexport */ store; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/interface/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "enableComplementaryArea", function() { return actions_enableComplementaryArea; });
__webpack_require__.d(actions_namespaceObject, "disableComplementaryArea", function() { return actions_disableComplementaryArea; });
__webpack_require__.d(actions_namespaceObject, "pinItem", function() { return actions_pinItem; });
__webpack_require__.d(actions_namespaceObject, "unpinItem", function() { return actions_unpinItem; });
__webpack_require__.d(actions_namespaceObject, "toggleFeature", function() { return actions_toggleFeature; });
__webpack_require__.d(actions_namespaceObject, "setFeatureValue", function() { return setFeatureValue; });
__webpack_require__.d(actions_namespaceObject, "setFeatureDefaults", function() { return setFeatureDefaults; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/interface/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getActiveComplementaryArea", function() { return selectors_getActiveComplementaryArea; });
__webpack_require__.d(selectors_namespaceObject, "isItemPinned", function() { return selectors_isItemPinned; });
__webpack_require__.d(selectors_namespaceObject, "isFeatureActive", function() { return isFeatureActive; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["gc","element"]
var external_gc_element_ = __webpack_require__("ewfG");

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external ["gc","components"]
var external_gc_components_ = __webpack_require__("jd0n");

// EXTERNAL MODULE: external ["gc","data"]
var external_gc_data_ = __webpack_require__("IgLd");

// EXTERNAL MODULE: external ["gc","i18n"]
var external_gc_i18n_ = __webpack_require__("z4sU");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/check.js
var check = __webpack_require__("Tv9K");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/star-filled.js
var star_filled = __webpack_require__("8mSS");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/star-empty.js
var star_empty = __webpack_require__("lqn5");

// EXTERNAL MODULE: external ["gc","viewport"]
var external_gc_viewport_ = __webpack_require__("3BOu");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/close-small.js
var close_small = __webpack_require__("Mg0k");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */


/**
 * Reducer to keep tract of the active area per scope.
 *
 * @param {boolean} state           Previous state.
 * @param {Object}  action          Action object.
 * @param {string}  action.type     Action type.
 * @param {string}  action.itemType Type of item.
 * @param {string}  action.scope    Item scope.
 * @param {string}  action.item     Item name.
 *
 * @return {Object} Updated state.
 */

function singleEnableItems() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let {
    type,
    itemType,
    scope,
    item
  } = arguments.length > 1 ? arguments[1] : undefined;

  if (type !== 'SET_SINGLE_ENABLE_ITEM' || !itemType || !scope) {
    return state;
  }

  return { ...state,
    [itemType]: { ...state[itemType],
      [scope]: item || null
    }
  };
}
/**
 * Reducer keeping track of the "pinned" items per scope.
 *
 * @param {boolean} state           Previous state.
 * @param {Object}  action          Action object.
 * @param {string}  action.type     Action type.
 * @param {string}  action.itemType Type of item.
 * @param {string}  action.scope    Item scope.
 * @param {string}  action.item     Item name.
 * @param {boolean} action.isEnable Whether the item is pinned.
 *
 * @return {Object} Updated state.
 */

function multipleEnableItems() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let {
    type,
    itemType,
    scope,
    item,
    isEnable
  } = arguments.length > 1 ? arguments[1] : undefined;

  if (type !== 'SET_MULTIPLE_ENABLE_ITEM' || !itemType || !scope || !item || Object(external_lodash_["get"])(state, [itemType, scope, item]) === isEnable) {
    return state;
  }

  const currentTypeState = state[itemType] || {};
  const currentScopeState = currentTypeState[scope] || {};
  return { ...state,
    [itemType]: { ...currentTypeState,
      [scope]: { ...currentScopeState,
        [item]: isEnable || false
      }
    }
  };
}
/**
 * Reducer returning the defaults for user preferences.
 *
 * This is kept intentionally separate from the preferences
 * themselves so that defaults are not persisted.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const preferenceDefaults = Object(external_gc_data_["combineReducers"])({
  features() {
    let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    let action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'SET_FEATURE_DEFAULTS') {
      const {
        scope,
        defaults
      } = action;
      return { ...state,
        [scope]: { ...state[scope],
          ...defaults
        }
      };
    }

    return state;
  }

});
/**
 * Reducer returning the user preferences.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const preferences = Object(external_gc_data_["combineReducers"])({
  features() {
    let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    let action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'SET_FEATURE_VALUE') {
      const {
        scope,
        featureName,
        value
      } = action;
      return { ...state,
        [scope]: { ...state[scope],
          [featureName]: value
        }
      };
    }

    return state;
  }

});
const enableItems = Object(external_gc_data_["combineReducers"])({
  singleEnableItems,
  multipleEnableItems
});
/* harmony default export */ var reducer = (Object(external_gc_data_["combineReducers"])({
  enableItems,
  preferenceDefaults,
  preferences
}));

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/actions.js
/**
 * Returns an action object used in signalling that an active area should be changed.
 *
 * @param {string} itemType Type of item.
 * @param {string} scope    Item scope.
 * @param {string} item     Item identifier.
 *
 * @return {Object} Action object.
 */
function setSingleEnableItem(itemType, scope, item) {
  return {
    type: 'SET_SINGLE_ENABLE_ITEM',
    itemType,
    scope,
    item
  };
}
/**
 * Returns an action object used in signalling that a complementary item should be enabled.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 *
 * @return {Object} Action object.
 */


function actions_enableComplementaryArea(scope, area) {
  return setSingleEnableItem('complementaryArea', scope, area);
}
/**
 * Returns an action object used in signalling that the complementary area of a given scope should be disabled.
 *
 * @param {string} scope Complementary area scope.
 *
 * @return {Object} Action object.
 */

function actions_disableComplementaryArea(scope) {
  return setSingleEnableItem('complementaryArea', scope, undefined);
}
/**
 * Returns an action object to make an area enabled/disabled.
 *
 * @param {string}  itemType Type of item.
 * @param {string}  scope    Item scope.
 * @param {string}  item     Item identifier.
 * @param {boolean} isEnable Boolean indicating if an area should be pinned or not.
 *
 * @return {Object} Action object.
 */

function setMultipleEnableItem(itemType, scope, item, isEnable) {
  return {
    type: 'SET_MULTIPLE_ENABLE_ITEM',
    itemType,
    scope,
    item,
    isEnable
  };
}
/**
 * Returns an action object used in signalling that an item should be pinned.
 *
 * @param {string} scope  Item scope.
 * @param {string} itemId Item identifier.
 *
 * @return {Object} Action object.
 */


function actions_pinItem(scope, itemId) {
  return setMultipleEnableItem('pinnedItems', scope, itemId, true);
}
/**
 * Returns an action object used in signalling that an item should be unpinned.
 *
 * @param {string} scope  Item scope.
 * @param {string} itemId Item identifier.
 *
 * @return {Object} Action object.
 */

function actions_unpinItem(scope, itemId) {
  return setMultipleEnableItem('pinnedItems', scope, itemId, false);
}
/**
 * Returns an action object used in signalling that a feature should be toggled.
 *
 * @param {string} scope       The feature scope (e.g. core/edit-post).
 * @param {string} featureName The feature name.
 */

function actions_toggleFeature(scope, featureName) {
  return function (_ref) {
    let {
      select,
      dispatch
    } = _ref;
    const currentValue = select.isFeatureActive(scope, featureName);
    dispatch.setFeatureValue(scope, featureName, !currentValue);
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
  return {
    type: 'SET_FEATURE_VALUE',
    scope,
    featureName,
    value: !!value
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
  return {
    type: 'SET_FEATURE_DEFAULTS',
    scope,
    defaults
  };
}

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * Returns the item that is enabled in a given scope.
 *
 * @param {Object} state    Global application state.
 * @param {string} itemType Type of item.
 * @param {string} scope    Item scope.
 *
 * @return {?string|null} The item that is enabled in the passed scope and type.
 */

function getSingleEnableItem(state, itemType, scope) {
  return Object(external_lodash_["get"])(state.enableItems.singleEnableItems, [itemType, scope]);
}
/**
 * Returns the complementary area that is active in a given scope.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Item scope.
 *
 * @return {string} The complementary area that is active in the given scope.
 */


function selectors_getActiveComplementaryArea(state, scope) {
  return getSingleEnableItem(state, 'complementaryArea', scope);
}
/**
 * Returns a boolean indicating if an item is enabled or not in a given scope.
 *
 * @param {Object} state    Global application state.
 * @param {string} itemType Type of item.
 * @param {string} scope    Scope.
 * @param {string} item     Item to check.
 *
 * @return {boolean|undefined} True if the item is enabled, false otherwise if the item is explicitly disabled, and undefined if there is no information for that item.
 */

function isMultipleEnabledItemEnabled(state, itemType, scope, item) {
  return Object(external_lodash_["get"])(state.enableItems.multipleEnableItems, [itemType, scope, item]);
}
/**
 * Returns a boolean indicating if an item is pinned or not.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Scope.
 * @param {string} item  Item to check.
 *
 * @return {boolean} True if the item is pinned and false otherwise.
 */


function selectors_isItemPinned(state, scope, item) {
  return isMultipleEnabledItemEnabled(state, 'pinnedItems', scope, item) !== false;
}
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

function isFeatureActive(state, scope, featureName) {
  var _state$preferences$fe, _state$preferenceDefa;

  const featureValue = (_state$preferences$fe = state.preferences.features[scope]) === null || _state$preferences$fe === void 0 ? void 0 : _state$preferences$fe[featureName];
  const defaultedFeatureValue = featureValue !== undefined ? featureValue : (_state$preferenceDefa = state.preferenceDefaults.features[scope]) === null || _state$preferenceDefa === void 0 ? void 0 : _state$preferenceDefa[featureName];
  return !!defaultedFeatureValue;
}

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/interface';

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/store/index.js
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

const store = Object(external_gc_data_["createReduxStore"])(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['enableItems', 'preferences'],
  __experimentalUseThunks: true
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

Object(external_gc_data_["registerStore"])(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['enableItems', 'preferences'],
  __experimentalUseThunks: true
});

// EXTERNAL MODULE: external ["gc","plugins"]
var external_gc_plugins_ = __webpack_require__("Xm27");

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-context/index.js
/**
 * GeChiUI dependencies
 */

/* harmony default export */ var complementary_area_context = (Object(external_gc_plugins_["withPluginContext"])((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon,
    identifier: ownProps.identifier || `${context.name}/${ownProps.name}`
  };
}));

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-toggle/index.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */




function ComplementaryAreaToggle(_ref) {
  let {
    as = external_gc_components_["Button"],
    scope,
    identifier,
    icon,
    selectedIcon,
    ...props
  } = _ref;
  const ComponentToUse = as;
  const isSelected = Object(external_gc_data_["useSelect"])(select => select(store).getActiveComplementaryArea(scope) === identifier, [identifier]);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = Object(external_gc_data_["useDispatch"])(store);
  return Object(external_gc_element_["createElement"])(ComponentToUse, Object(esm_extends["a" /* default */])({
    icon: selectedIcon && isSelected ? selectedIcon : icon,
    onClick: () => {
      if (isSelected) {
        disableComplementaryArea(scope);
      } else {
        enableComplementaryArea(scope, identifier);
      }
    }
  }, Object(external_lodash_["omit"])(props, ['name'])));
}

/* harmony default export */ var complementary_area_toggle = (complementary_area_context(ComplementaryAreaToggle));

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-header/index.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */



const ComplementaryAreaHeader = _ref => {
  let {
    smallScreenTitle,
    children,
    className,
    toggleButtonProps
  } = _ref;
  const toggleButton = Object(external_gc_element_["createElement"])(complementary_area_toggle, Object(esm_extends["a" /* default */])({
    icon: close_small["a" /* default */]
  }, toggleButtonProps));
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("div", {
    className: "components-panel__header interface-complementary-area-header__small"
  }, smallScreenTitle && Object(external_gc_element_["createElement"])("span", {
    className: "interface-complementary-area-header__small-title"
  }, smallScreenTitle), toggleButton), Object(external_gc_element_["createElement"])("div", {
    className: classnames_default()('components-panel__header', 'interface-complementary-area-header', className),
    tabIndex: -1
  }, children, toggleButton));
};

/* harmony default export */ var complementary_area_header = (ComplementaryAreaHeader);

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/action-item/index.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */




function ActionItemSlot(_ref) {
  let {
    name,
    as: Component = external_gc_components_["ButtonGroup"],
    fillProps = {},
    bubblesVirtually,
    ...props
  } = _ref;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Slot"], {
    name: name,
    bubblesVirtually: bubblesVirtually,
    fillProps: fillProps
  }, fills => {
    if (Object(external_lodash_["isEmpty"])(external_gc_element_["Children"].toArray(fills))) {
      return null;
    } // Special handling exists for backward compatibility.
    // It ensures that menu items created by plugin authors aren't
    // duplicated with automatically injected menu items coming
    // from pinnable plugin sidebars.
    // @see https://github.com/GeChiUI/gutenberg/issues/14457


    const initializedByPlugins = [];
    external_gc_element_["Children"].forEach(fills, _ref2 => {
      let {
        props: {
          __unstableExplicitMenuItem,
          __unstableTarget
        }
      } = _ref2;

      if (__unstableTarget && __unstableExplicitMenuItem) {
        initializedByPlugins.push(__unstableTarget);
      }
    });
    const children = external_gc_element_["Children"].map(fills, child => {
      if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(child.props.__unstableTarget)) {
        return null;
      }

      return child;
    });
    return Object(external_gc_element_["createElement"])(Component, props, children);
  });
}

function ActionItem(_ref3) {
  let {
    name,
    as: Component = external_gc_components_["Button"],
    onClick,
    ...props
  } = _ref3;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Fill"], {
    name: name
  }, _ref4 => {
    let {
      onClick: fpOnClick
    } = _ref4;
    return Object(external_gc_element_["createElement"])(Component, Object(esm_extends["a" /* default */])({
      onClick: onClick || fpOnClick ? function () {
        (onClick || external_lodash_["noop"])(...arguments);
        (fpOnClick || external_lodash_["noop"])(...arguments);
      } : undefined
    }, props));
  });
}

ActionItem.Slot = ActionItemSlot;
/* harmony default export */ var action_item = (ActionItem);

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area-more-menu-item/index.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */




const PluginsMenuItem = props => // Menu item is marked with unstable prop for backward compatibility.
// They are removed so they don't leak to DOM elements.
// @see https://github.com/GeChiUI/gutenberg/issues/14457
Object(external_gc_element_["createElement"])(external_gc_components_["MenuItem"], Object(external_lodash_["omit"])(props, ['__unstableExplicitMenuItem', '__unstableTarget']));

function ComplementaryAreaMoreMenuItem(_ref) {
  let {
    scope,
    target,
    __unstableExplicitMenuItem,
    ...props
  } = _ref;
  return Object(external_gc_element_["createElement"])(complementary_area_toggle, Object(esm_extends["a" /* default */])({
    as: toggleProps => {
      return Object(external_gc_element_["createElement"])(action_item, Object(esm_extends["a" /* default */])({
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`
      }, toggleProps));
    },
    role: "menuitemcheckbox",
    selectedIcon: check["a" /* default */],
    name: target,
    scope: scope
  }, props));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/pinned-items/index.js



/**
 * External dependencies
 */


/**
 * GeChiUI dependencies
 */



function PinnedItems(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Fill"], Object(esm_extends["a" /* default */])({
    name: `PinnedItems/${scope}`
  }, props));
}

function PinnedItemsSlot(_ref2) {
  let {
    scope,
    className,
    ...props
  } = _ref2;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Slot"], Object(esm_extends["a" /* default */])({
    name: `PinnedItems/${scope}`
  }, props), fills => !Object(external_lodash_["isEmpty"])(fills) && Object(external_gc_element_["createElement"])("div", {
    className: classnames_default()(className, 'interface-pinned-items')
  }, fills));
}

PinnedItems.Slot = PinnedItemsSlot;
/* harmony default export */ var pinned_items = (PinnedItems);

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/complementary-area/index.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */








function ComplementaryAreaSlot(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Slot"], Object(esm_extends["a" /* default */])({
    name: `ComplementaryArea/${scope}`
  }, props));
}

function ComplementaryAreaFill(_ref2) {
  let {
    scope,
    children,
    className
  } = _ref2;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Fill"], {
    name: `ComplementaryArea/${scope}`
  }, Object(external_gc_element_["createElement"])("div", {
    className: className
  }, children));
}

function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
  const previousIsSmall = Object(external_gc_element_["useRef"])(false);
  const shouldOpenWhenNotSmall = Object(external_gc_element_["useRef"])(false);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = Object(external_gc_data_["useDispatch"])(store);
  Object(external_gc_element_["useEffect"])(() => {
    // If the complementary area is active and the editor is switching from a big to a small window size.
    if (isActive && isSmall && !previousIsSmall.current) {
      // Disable the complementary area.
      disableComplementaryArea(scope); // Flag the complementary area to be reopened when the window size goes from small to big.

      shouldOpenWhenNotSmall.current = true;
    } else if ( // If there is a flag indicating the complementary area should be enabled when we go from small to big window size
    // and we are going from a small to big window size.
    shouldOpenWhenNotSmall.current && !isSmall && previousIsSmall.current) {
      // Remove the flag indicating the complementary area should be enabled.
      shouldOpenWhenNotSmall.current = false; // Enable the complementary area.

      enableComplementaryArea(scope, identifier);
    } else if ( // If the flag is indicating the current complementary should be reopened but another complementary area becomes active,
    // remove the flag.
    shouldOpenWhenNotSmall.current && activeArea && activeArea !== identifier) {
      shouldOpenWhenNotSmall.current = false;
    }

    if (isSmall !== previousIsSmall.current) {
      previousIsSmall.current = isSmall;
    }
  }, [isActive, isSmall, scope, identifier, activeArea]);
}

function ComplementaryArea(_ref3) {
  let {
    children,
    className,
    closeLabel = Object(external_gc_i18n_["__"])('关闭插件'),
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
  } = _ref3;
  const {
    isActive,
    isPinned,
    activeArea,
    isSmall,
    isLarge
  } = Object(external_gc_data_["useSelect"])(select => {
    const {
      getActiveComplementaryArea,
      isItemPinned
    } = select(store);

    const _activeArea = getActiveComplementaryArea(scope);

    return {
      isActive: _activeArea === identifier,
      isPinned: isItemPinned(scope, identifier),
      activeArea: _activeArea,
      isSmall: select(external_gc_viewport_["store"]).isViewportMatch('< medium'),
      isLarge: select(external_gc_viewport_["store"]).isViewportMatch('large')
    };
  }, [identifier, scope]);
  useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall);
  const {
    enableComplementaryArea,
    disableComplementaryArea,
    pinItem,
    unpinItem
  } = Object(external_gc_data_["useDispatch"])(store);
  Object(external_gc_element_["useEffect"])(() => {
    if (isActiveByDefault && activeArea === undefined && !isSmall) {
      enableComplementaryArea(scope, identifier);
    }
  }, [activeArea, isActiveByDefault, scope, identifier, isSmall]);
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, isPinnable && Object(external_gc_element_["createElement"])(pinned_items, {
    scope: scope
  }, isPinned && Object(external_gc_element_["createElement"])(complementary_area_toggle, {
    scope: scope,
    identifier: identifier,
    isPressed: isActive && (!showIconLabels || isLarge),
    "aria-expanded": isActive,
    label: title,
    icon: showIconLabels ? check["a" /* default */] : icon,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  })), name && isPinnable && Object(external_gc_element_["createElement"])(ComplementaryAreaMoreMenuItem, {
    target: name,
    scope: scope,
    icon: icon
  }, title), isActive && Object(external_gc_element_["createElement"])(ComplementaryAreaFill, {
    className: classnames_default()('interface-complementary-area', className),
    scope: scope
  }, Object(external_gc_element_["createElement"])(complementary_area_header, {
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
  }, header || Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("strong", null, title), isPinnable && Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
    className: "interface-complementary-area__pin-unpin-item",
    icon: isPinned ? star_filled["a" /* default */] : star_empty["a" /* default */],
    label: isPinned ? Object(external_gc_i18n_["__"])('从工具栏移除') : Object(external_gc_i18n_["__"])('固定至工具栏'),
    onClick: () => (isPinned ? unpinItem : pinItem)(scope, identifier),
    isPressed: isPinned,
    "aria-expanded": isPinned
  }))), Object(external_gc_element_["createElement"])(external_gc_components_["Panel"], {
    className: panelClassName
  }, children)));
}

const ComplementaryAreaWrapped = complementary_area_context(ComplementaryArea);
ComplementaryAreaWrapped.Slot = ComplementaryAreaSlot;
/* harmony default export */ var complementary_area = (ComplementaryAreaWrapped);

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/fullscreen-mode/index.js
/**
 * GeChiUI dependencies
 */


const FullscreenMode = _ref => {
  let {
    isActive
  } = _ref;
  Object(external_gc_element_["useEffect"])(() => {
    let isSticky = false; // `is-fullscreen-mode` is set in PHP as a body class by Gutenberg, and this causes
    // `sticky-menu` to be applied by GeChiUI and prevents the admin menu being scrolled
    // even if `is-fullscreen-mode` is then removed. Let's remove `sticky-menu` here as
    // a consequence of the FullscreenMode setup

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
  Object(external_gc_element_["useEffect"])(() => {
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

// EXTERNAL MODULE: external ["gc","compose"]
var external_gc_compose_ = __webpack_require__("dMTb");

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/interface-skeleton/index.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */

/**
 * GeChiUI dependencies
 */






function useHTMLClass(className) {
  Object(external_gc_element_["useEffect"])(() => {
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

function InterfaceSkeleton(_ref, ref) {
  let {
    footer,
    header,
    sidebar,
    secondarySidebar,
    notices,
    content,
    drawer,
    actions,
    labels,
    className,
    shortcuts
  } = _ref;
  const navigateRegionsProps = Object(external_gc_components_["__unstableUseNavigateRegions"])(shortcuts);
  useHTMLClass('interface-interface-skeleton__html-container');
  const defaultLabels = {
    /* translators: accessibility text for the nav bar landmark region. */
    drawer: Object(external_gc_i18n_["__"])('抽屉'),

    /* translators: accessibility text for the top bar landmark region. */
    header: Object(external_gc_i18n_["__"])('页眉'),

    /* translators: accessibility text for the content landmark region. */
    body: Object(external_gc_i18n_["__"])('内容'),

    /* translators: accessibility text for the secondary sidebar landmark region. */
    secondarySidebar: Object(external_gc_i18n_["__"])('区块库'),

    /* translators: accessibility text for the settings landmark region. */
    sidebar: Object(external_gc_i18n_["__"])('设置'),

    /* translators: accessibility text for the publish landmark region. */
    actions: Object(external_gc_i18n_["__"])('发布'),

    /* translators: accessibility text for the footer landmark region. */
    footer: Object(external_gc_i18n_["__"])('页脚')
  };
  const mergedLabels = { ...defaultLabels,
    ...labels
  };
  return Object(external_gc_element_["createElement"])("div", Object(esm_extends["a" /* default */])({}, navigateRegionsProps, {
    ref: Object(external_gc_compose_["useMergeRefs"])([ref, navigateRegionsProps.ref]),
    className: classnames_default()(className, 'interface-interface-skeleton', navigateRegionsProps.className, !!footer && 'has-footer')
  }), !!drawer && Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__drawer",
    role: "region",
    "aria-label": mergedLabels.drawer,
    tabIndex: "-1"
  }, drawer), Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__editor"
  }, !!header && Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__header",
    role: "region",
    "aria-label": mergedLabels.header,
    tabIndex: "-1"
  }, header), Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__body"
  }, !!secondarySidebar && Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__secondary-sidebar",
    role: "region",
    "aria-label": mergedLabels.secondarySidebar,
    tabIndex: "-1"
  }, secondarySidebar), !!notices && Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__notices"
  }, notices), Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__content",
    role: "region",
    "aria-label": mergedLabels.body,
    tabIndex: "-1"
  }, content), !!sidebar && Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__sidebar",
    role: "region",
    "aria-label": mergedLabels.sidebar,
    tabIndex: "-1"
  }, sidebar), !!actions && Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__actions",
    role: "region",
    "aria-label": mergedLabels.actions,
    tabIndex: "-1"
  }, actions))), !!footer && Object(external_gc_element_["createElement"])("div", {
    className: "interface-interface-skeleton__footer",
    role: "region",
    "aria-label": mergedLabels.footer,
    tabIndex: "-1"
  }, footer));
}

/* harmony default export */ var interface_skeleton = (Object(external_gc_element_["forwardRef"])(InterfaceSkeleton));

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/more-vertical.js
var more_vertical = __webpack_require__("eTuh");

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/more-menu-dropdown/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */




function MoreMenuDropdown(_ref) {
  let {
    as: DropdownComponent = external_gc_components_["DropdownMenu"],
    className,

    /* translators: button label text should, if possible, be under 16 characters. */
    label = Object(external_gc_i18n_["__"])('选项'),
    popoverProps,
    toggleProps,
    children
  } = _ref;
  return Object(external_gc_element_["createElement"])(DropdownComponent, {
    className: classnames_default()('interface-more-menu-dropdown', className),
    icon: more_vertical["a" /* default */],
    label: label,
    popoverProps: {
      position: 'bottom left',
      ...popoverProps,
      className: classnames_default()('interface-more-menu-dropdown__content', popoverProps === null || popoverProps === void 0 ? void 0 : popoverProps.className)
    },
    toggleProps: {
      tooltipPosition: 'bottom',
      ...toggleProps
    }
  }, onClose => children(onClose));
}

// EXTERNAL MODULE: external ["gc","a11y"]
var external_gc_a11y_ = __webpack_require__("NQKH");

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/more-menu-feature-toggle/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


function MoreMenuFeatureToggle(_ref) {
  let {
    scope,
    label,
    info,
    messageActivated,
    messageDeactivated,
    shortcut,
    feature
  } = _ref;
  const isActive = Object(external_gc_data_["useSelect"])(select => select(store).isFeatureActive(scope, feature), [feature]);
  const {
    toggleFeature
  } = Object(external_gc_data_["useDispatch"])(store);

  const speakMessage = () => {
    if (isActive) {
      Object(external_gc_a11y_["speak"])(messageDeactivated || Object(external_gc_i18n_["__"])('功能已禁用'));
    } else {
      Object(external_gc_a11y_["speak"])(messageActivated || Object(external_gc_i18n_["__"])('功能已启用'));
    }
  };

  return Object(external_gc_element_["createElement"])(external_gc_components_["MenuItem"], {
    icon: isActive && check["a" /* default */],
    isSelected: isActive,
    onClick: () => {
      toggleFeature(scope, feature);
      speakMessage();
    },
    role: "menuitemcheckbox",
    info: info,
    shortcut: shortcut
  }, label);
}

// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/components/index.js









// CONCATENATED MODULE: ./node_modules/@gechiui/interface/build-module/index.js




/***/ }),

/***/ "3BOu":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["viewport"]; }());

/***/ }),

/***/ "8mSS":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const starFilled = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ __webpack_exports__["a"] = (starFilled);


/***/ }),

/***/ "EXvS":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["dom"]; }());

/***/ }),

/***/ "Gz8V":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["primitives"]; }());

/***/ }),

/***/ "IgLd":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["data"]; }());

/***/ }),

/***/ "IuOC":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["keyboardShortcuts"]; }());

/***/ }),

/***/ "Mg0k":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const closeSmall = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ __webpack_exports__["a"] = (closeSmall);


/***/ }),

/***/ "NQKH":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["a11y"]; }());

/***/ }),

/***/ "NxuN":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["coreData"]; }());

/***/ }),

/***/ "OwI1":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["widgets"]; }());

/***/ }),

/***/ "Ppya":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["reusableBlocks"]; }());

/***/ }),

/***/ "TSYQ":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

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
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
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


/***/ }),

/***/ "Tv9K":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const check = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (check);


/***/ }),

/***/ "W2Kb":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["notices"]; }());

/***/ }),

/***/ "WNKN":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const redo = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (redo);


/***/ }),

/***/ "Xm27":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["plugins"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "Yjv4":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["mediaUtils"]; }());

/***/ }),

/***/ "ajyK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const close = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (close);


/***/ }),

/***/ "dMTb":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["compose"]; }());

/***/ }),

/***/ "di29":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "reinitializeEditor", function() { return /* binding */ reinitializeEditor; });
__webpack_require__.d(__webpack_exports__, "initialize", function() { return /* binding */ initialize; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/edit-widgets/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "persistStubPost", function() { return persistStubPost; });
__webpack_require__.d(actions_namespaceObject, "saveEditedWidgetAreas", function() { return actions_saveEditedWidgetAreas; });
__webpack_require__.d(actions_namespaceObject, "saveWidgetAreas", function() { return saveWidgetAreas; });
__webpack_require__.d(actions_namespaceObject, "saveWidgetArea", function() { return saveWidgetArea; });
__webpack_require__.d(actions_namespaceObject, "setWidgetIdForClientId", function() { return setWidgetIdForClientId; });
__webpack_require__.d(actions_namespaceObject, "setWidgetAreasOpenState", function() { return setWidgetAreasOpenState; });
__webpack_require__.d(actions_namespaceObject, "setIsWidgetAreaOpen", function() { return actions_setIsWidgetAreaOpen; });
__webpack_require__.d(actions_namespaceObject, "setIsInserterOpened", function() { return actions_setIsInserterOpened; });
__webpack_require__.d(actions_namespaceObject, "setIsListViewOpened", function() { return actions_setIsListViewOpened; });
__webpack_require__.d(actions_namespaceObject, "closeGeneralSidebar", function() { return actions_closeGeneralSidebar; });
__webpack_require__.d(actions_namespaceObject, "moveBlockToWidgetArea", function() { return actions_moveBlockToWidgetArea; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/edit-widgets/build-module/store/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, "getWidgetAreas", function() { return getWidgetAreas; });
__webpack_require__.d(resolvers_namespaceObject, "getWidgets", function() { return getWidgets; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/edit-widgets/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getWidgets", function() { return selectors_getWidgets; });
__webpack_require__.d(selectors_namespaceObject, "getWidget", function() { return getWidget; });
__webpack_require__.d(selectors_namespaceObject, "getWidgetAreas", function() { return selectors_getWidgetAreas; });
__webpack_require__.d(selectors_namespaceObject, "getWidgetAreaForWidgetId", function() { return getWidgetAreaForWidgetId; });
__webpack_require__.d(selectors_namespaceObject, "getParentWidgetAreaBlock", function() { return selectors_getParentWidgetAreaBlock; });
__webpack_require__.d(selectors_namespaceObject, "getEditedWidgetAreas", function() { return selectors_getEditedWidgetAreas; });
__webpack_require__.d(selectors_namespaceObject, "getReferenceWidgetBlocks", function() { return getReferenceWidgetBlocks; });
__webpack_require__.d(selectors_namespaceObject, "isSavingWidgetAreas", function() { return selectors_isSavingWidgetAreas; });
__webpack_require__.d(selectors_namespaceObject, "getIsWidgetAreaOpen", function() { return getIsWidgetAreaOpen; });
__webpack_require__.d(selectors_namespaceObject, "isInserterOpened", function() { return selectors_isInserterOpened; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetInsertionPoint", function() { return __experimentalGetInsertionPoint; });
__webpack_require__.d(selectors_namespaceObject, "canInsertBlockInWidgetArea", function() { return selectors_canInsertBlockInWidgetArea; });
__webpack_require__.d(selectors_namespaceObject, "isListViewOpened", function() { return selectors_isListViewOpened; });

// NAMESPACE OBJECT: ./node_modules/@gechiui/edit-widgets/build-module/blocks/widget-area/index.js
var widget_area_namespaceObject = {};
__webpack_require__.r(widget_area_namespaceObject);
__webpack_require__.d(widget_area_namespaceObject, "metadata", function() { return widget_area_metadata; });
__webpack_require__.d(widget_area_namespaceObject, "name", function() { return widget_area_name; });
__webpack_require__.d(widget_area_namespaceObject, "settings", function() { return widget_area_settings; });

// EXTERNAL MODULE: external ["gc","element"]
var external_gc_element_ = __webpack_require__("ewfG");

// EXTERNAL MODULE: external ["gc","blocks"]
var external_gc_blocks_ = __webpack_require__("n68F");

// EXTERNAL MODULE: external ["gc","data"]
var external_gc_data_ = __webpack_require__("IgLd");

// EXTERNAL MODULE: external ["gc","blockLibrary"]
var external_gc_blockLibrary_ = __webpack_require__("u+Ow");

// EXTERNAL MODULE: external ["gc","coreData"]
var external_gc_coreData_ = __webpack_require__("NxuN");

// EXTERNAL MODULE: external ["gc","widgets"]
var external_gc_widgets_ = __webpack_require__("OwI1");

// EXTERNAL MODULE: ./node_modules/@gechiui/interface/build-module/index.js + 17 modules
var build_module = __webpack_require__("2YC4");

// EXTERNAL MODULE: external ["gc","apiFetch"]
var external_gc_apiFetch_ = __webpack_require__("xuem");
var external_gc_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_gc_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/reducer.js
/**
 * GeChiUI dependencies
 */

/**
 * Controls the open state of the widget areas.
 *
 * @param {Object} state  Redux state.
 * @param {Object} action Redux action.
 *
 * @return {Array} Updated state.
 */

function reducer_widgetAreasOpenState() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;
  const {
    type
  } = action;

  switch (type) {
    case 'SET_WIDGET_AREAS_OPEN_STATE':
      {
        return action.widgetAreasOpenState;
      }

    case 'SET_IS_WIDGET_AREA_OPEN':
      {
        const {
          clientId,
          isOpen
        } = action;
        return { ...state,
          [clientId]: isOpen
        };
      }

    default:
      {
        return state;
      }
  }
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

function blockInserterPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

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

function listViewPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value ? false : state;

    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen;
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_gc_data_["combineReducers"])({
  blockInserterPanel,
  listViewPanel,
  widgetAreasOpenState: reducer_widgetAreasOpenState
}));

// EXTERNAL MODULE: external ["gc","i18n"]
var external_gc_i18n_ = __webpack_require__("z4sU");

// EXTERNAL MODULE: external ["gc","notices"]
var external_gc_notices_ = __webpack_require__("W2Kb");

// EXTERNAL MODULE: external ["gc","blockEditor"]
var external_gc_blockEditor_ = __webpack_require__("nLrk");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/transformers.js
/**
 * GeChiUI dependencies
 */


/**
 * Converts a widget entity record into a block.
 *
 * @param {Object} widget The widget entity record.
 * @return {Object} a block (converted from the entity record).
 */

function transformWidgetToBlock(widget) {
  if (widget.id_base === 'block') {
    const parsedBlocks = Object(external_gc_blocks_["parse"])(widget.instance.raw.content);

    if (!parsedBlocks.length) {
      return Object(external_gc_widgets_["addWidgetIdToBlock"])(Object(external_gc_blocks_["createBlock"])('core/paragraph', {}, []), widget.id);
    }

    return Object(external_gc_widgets_["addWidgetIdToBlock"])(parsedBlocks[0], widget.id);
  }

  let attributes;

  if (widget._embedded.about[0].is_multi) {
    attributes = {
      idBase: widget.id_base,
      instance: widget.instance
    };
  } else {
    attributes = {
      id: widget.id
    };
  }

  return Object(external_gc_widgets_["addWidgetIdToBlock"])(Object(external_gc_blocks_["createBlock"])('core/legacy-widget', attributes, []), widget.id);
}
/**
 * Converts a block to a widget entity record.
 *
 * @param {Object}  block         The block.
 * @param {Object?} relatedWidget A related widget entity record from the API (optional).
 * @return {Object} the widget object (converted from block).
 */

function transformBlockToWidget(block) {
  let relatedWidget = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  let widget;
  const isValidLegacyWidgetBlock = block.name === 'core/legacy-widget' && (block.attributes.id || block.attributes.instance);

  if (isValidLegacyWidgetBlock) {
    var _block$attributes$id, _block$attributes$idB, _block$attributes$ins;

    widget = { ...relatedWidget,
      id: (_block$attributes$id = block.attributes.id) !== null && _block$attributes$id !== void 0 ? _block$attributes$id : relatedWidget.id,
      id_base: (_block$attributes$idB = block.attributes.idBase) !== null && _block$attributes$idB !== void 0 ? _block$attributes$idB : relatedWidget.id_base,
      instance: (_block$attributes$ins = block.attributes.instance) !== null && _block$attributes$ins !== void 0 ? _block$attributes$ins : relatedWidget.instance
    };
  } else {
    widget = { ...relatedWidget,
      id_base: 'block',
      instance: {
        raw: {
          content: Object(external_gc_blocks_["serialize"])(block)
        }
      }
    };
  } // Delete read-only properties.


  delete widget.rendered;
  delete widget.rendered_form;
  return widget;
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/utils.js
/**
 * "Kind" of the navigation post.
 *
 * @type {string}
 */
const KIND = 'root';
/**
 * "post type" of the navigation post.
 *
 * @type {string}
 */

const WIDGET_AREA_ENTITY_TYPE = 'sidebar';
/**
 * "post type" of the widget area post.
 *
 * @type {string}
 */

const POST_TYPE = 'postType';
/**
 * Builds an ID for a new widget area post.
 *
 * @param {number} widgetAreaId Widget area id.
 * @return {string} An ID.
 */

const buildWidgetAreaPostId = widgetAreaId => `widget-area-${widgetAreaId}`;
/**
 * Builds an ID for a global widget areas post.
 *
 * @return {string} An ID.
 */

const buildWidgetAreasPostId = () => `widget-areas`;
/**
 * Builds a query to resolve sidebars.
 *
 * @return {Object} Query.
 */

function buildWidgetAreasQuery() {
  return {
    per_page: -1
  };
}
/**
 * Builds a query to resolve widgets.
 *
 * @return {Object} Query.
 */

function buildWidgetsQuery() {
  return {
    per_page: -1,
    _embed: 'about'
  };
}
/**
 * Creates a stub post with given id and set of blocks. Used as a governing entity records
 * for all widget areas.
 *
 * @param {string} id     Post ID.
 * @param {Array}  blocks The list of blocks.
 * @return {Object} A stub post object formatted in compliance with the data layer.
 */

const createStubPost = (id, blocks) => ({
  id,
  slug: id,
  status: 'draft',
  type: 'page',
  blocks,
  meta: {
    widgetAreaId: id
  }
});

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/constants.js
/**
 * Module Constants
 */
const STORE_NAME = 'core/edit-widgets';

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/actions.js
/**
 * GeChiUI dependencies
 */






/**
 * Internal dependencies
 */




/**
 * Persists a stub post with given ID to core data store. The post is meant to be in-memory only and
 * shouldn't be saved via the API.
 *
 * @param {string} id     Post ID.
 * @param {Array}  blocks Blocks the post should consist of.
 * @return {Object} The post object.
 */

const persistStubPost = (id, blocks) => _ref => {
  let {
    registry
  } = _ref;
  const stubPost = createStubPost(id, blocks);
  registry.dispatch(external_gc_coreData_["store"]).receiveEntityRecords(KIND, POST_TYPE, stubPost, {
    id: stubPost.id
  }, false);
  return stubPost;
};
/**
 * Converts all the blocks from edited widget areas into widgets,
 * and submits a batch request to save everything at once.
 *
 * Creates a snackbar notice on either success or error.
 *
 * @return {Function} An action creator.
 */

const actions_saveEditedWidgetAreas = () => async _ref2 => {
  let {
    select,
    dispatch,
    registry
  } = _ref2;
  const editedWidgetAreas = select.getEditedWidgetAreas();

  if (!(editedWidgetAreas !== null && editedWidgetAreas !== void 0 && editedWidgetAreas.length)) {
    return;
  }

  try {
    await dispatch.saveWidgetAreas(editedWidgetAreas);
    registry.dispatch(external_gc_notices_["store"]).createSuccessNotice(Object(external_gc_i18n_["__"])('小工具已保存。'), {
      type: 'snackbar'
    });
  } catch (e) {
    registry.dispatch(external_gc_notices_["store"]).createErrorNotice(
    /* translators: %s: The error message. */
    Object(external_gc_i18n_["sprintf"])(Object(external_gc_i18n_["__"])('出现错误。 %s'), e.message), {
      type: 'snackbar'
    });
  }
};
/**
 * Converts all the blocks from specified widget areas into widgets,
 * and submits a batch request to save everything at once.
 *
 * @param {Object[]} widgetAreas Widget areas to save.
 * @return {Function} An action creator.
 */

const saveWidgetAreas = widgetAreas => async _ref3 => {
  let {
    dispatch,
    registry
  } = _ref3;

  try {
    for (const widgetArea of widgetAreas) {
      await dispatch.saveWidgetArea(widgetArea.id);
    }
  } finally {
    // saveEditedEntityRecord resets the resolution status, let's fix it manually
    await registry.dispatch(external_gc_coreData_["store"]).finishResolution('getEntityRecord', KIND, WIDGET_AREA_ENTITY_TYPE, buildWidgetAreasQuery());
  }
};
/**
 * Converts all the blocks from a widget area specified by ID into widgets,
 * and submits a batch request to save everything at once.
 *
 * @param {string} widgetAreaId ID of the widget area to process.
 * @return {Function} An action creator.
 */

const saveWidgetArea = widgetAreaId => async _ref4 => {
  let {
    dispatch,
    select,
    registry
  } = _ref4;
  const widgets = select.getWidgets();
  const post = registry.select(external_gc_coreData_["store"]).getEditedEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(widgetAreaId)); // Get all widgets from this area

  const areaWidgets = Object.values(widgets).filter(_ref5 => {
    let {
      sidebar
    } = _ref5;
    return sidebar === widgetAreaId;
  }); // Remove all duplicate reference widget instances for legacy widgets.
  // Why? We filter out the widgets with duplicate IDs to prevent adding more than one instance of a widget
  // implemented using a function. GeChiUI doesn't support having more than one instance of these, if you try to
  // save multiple instances of these in different sidebars you will run into undefined behaviors.

  const usedReferenceWidgets = [];
  const widgetsBlocks = post.blocks.filter(block => {
    const {
      id
    } = block.attributes;

    if (block.name === 'core/legacy-widget' && id) {
      if (usedReferenceWidgets.includes(id)) {
        return false;
      }

      usedReferenceWidgets.push(id);
    }

    return true;
  }); // Determine which widgets have been deleted. We can tell if a widget is
  // deleted and not just moved to a different area by looking to see if
  // getWidgetAreaForWidgetId() finds something.

  const deletedWidgets = [];

  for (const widget of areaWidgets) {
    const widgetsNewArea = select.getWidgetAreaForWidgetId(widget.id);

    if (!widgetsNewArea) {
      deletedWidgets.push(widget);
    }
  }

  const batchMeta = [];
  const batchTasks = [];
  const sidebarWidgetsIds = [];

  for (let i = 0; i < widgetsBlocks.length; i++) {
    const block = widgetsBlocks[i];
    const widgetId = Object(external_gc_widgets_["getWidgetIdFromBlock"])(block);
    const oldWidget = widgets[widgetId];
    const widget = transformBlockToWidget(block, oldWidget); // We'll replace the null widgetId after save, but we track it here
    // since order is important.

    sidebarWidgetsIds.push(widgetId); // Check oldWidget as widgetId might refer to an ID which has been
    // deleted, e.g. if a deleted block is restored via undo after saving.

    if (oldWidget) {
      // Update an existing widget.
      registry.dispatch(external_gc_coreData_["store"]).editEntityRecord('root', 'widget', widgetId, { ...widget,
        sidebar: widgetAreaId
      }, {
        undoIgnore: true
      });
      const hasEdits = registry.select(external_gc_coreData_["store"]).hasEditsForEntityRecord('root', 'widget', widgetId);

      if (!hasEdits) {
        continue;
      }

      batchTasks.push(_ref6 => {
        let {
          saveEditedEntityRecord
        } = _ref6;
        return saveEditedEntityRecord('root', 'widget', widgetId);
      });
    } else {
      // Create a new widget.
      batchTasks.push(_ref7 => {
        let {
          saveEntityRecord
        } = _ref7;
        return saveEntityRecord('root', 'widget', { ...widget,
          sidebar: widgetAreaId
        });
      });
    }

    batchMeta.push({
      block,
      position: i,
      clientId: block.clientId
    });
  }

  for (const widget of deletedWidgets) {
    batchTasks.push(_ref8 => {
      let {
        deleteEntityRecord
      } = _ref8;
      return deleteEntityRecord('root', 'widget', widget.id, {
        force: true
      });
    });
  }

  const records = await registry.dispatch(external_gc_coreData_["store"]).__experimentalBatch(batchTasks);
  const preservedRecords = records.filter(record => !record.hasOwnProperty('deleted'));
  const failedWidgetNames = [];

  for (let i = 0; i < preservedRecords.length; i++) {
    const widget = preservedRecords[i];
    const {
      block,
      position
    } = batchMeta[i]; // Set __internalWidgetId on the block. This will be persisted to the
    // store when we dispatch receiveEntityRecords( post ) below.

    post.blocks[position].attributes.__internalWidgetId = widget.id;
    const error = registry.select(external_gc_coreData_["store"]).getLastEntitySaveError('root', 'widget', widget.id);

    if (error) {
      var _block$attributes;

      failedWidgetNames.push(((_block$attributes = block.attributes) === null || _block$attributes === void 0 ? void 0 : _block$attributes.name) || (block === null || block === void 0 ? void 0 : block.name));
    }

    if (!sidebarWidgetsIds[position]) {
      sidebarWidgetsIds[position] = widget.id;
    }
  }

  if (failedWidgetNames.length) {
    throw new Error(Object(external_gc_i18n_["sprintf"])(
    /* translators: %s: List of widget names */
    Object(external_gc_i18n_["__"])('无法保存以下小工具：%s。'), failedWidgetNames.join(', ')));
  }

  registry.dispatch(external_gc_coreData_["store"]).editEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, widgetAreaId, {
    widgets: sidebarWidgetsIds
  }, {
    undoIgnore: true
  });
  dispatch(trySaveWidgetArea(widgetAreaId));
  registry.dispatch(external_gc_coreData_["store"]).receiveEntityRecords(KIND, POST_TYPE, post, undefined);
};

const trySaveWidgetArea = widgetAreaId => _ref9 => {
  let {
    registry
  } = _ref9;
  const saveErrorBefore = registry.select(external_gc_coreData_["store"]).getLastEntitySaveError(KIND, WIDGET_AREA_ENTITY_TYPE, widgetAreaId);
  registry.dispatch(external_gc_coreData_["store"]).saveEditedEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, widgetAreaId);
  const saveErrorAfter = registry.select(external_gc_coreData_["store"]).getLastEntitySaveError(KIND, WIDGET_AREA_ENTITY_TYPE, widgetAreaId);

  if (saveErrorAfter && saveErrorBefore !== saveErrorAfter) {
    throw new Error(saveErrorAfter);
  }
};
/**
 * Sets the clientId stored for a particular widgetId.
 *
 * @param {number} clientId Client id.
 * @param {number} widgetId Widget id.
 *
 * @return {Object} Action.
 */


function setWidgetIdForClientId(clientId, widgetId) {
  return {
    type: 'SET_WIDGET_ID_FOR_CLIENT_ID',
    clientId,
    widgetId
  };
}
/**
 * Sets the open state of all the widget areas.
 *
 * @param {Object} widgetAreasOpenState The open states of all the widget areas.
 *
 * @return {Object} Action.
 */

function setWidgetAreasOpenState(widgetAreasOpenState) {
  return {
    type: 'SET_WIDGET_AREAS_OPEN_STATE',
    widgetAreasOpenState
  };
}
/**
 * Sets the open state of the widget area.
 *
 * @param {string}  clientId The clientId of the widget area.
 * @param {boolean} isOpen   Whether the widget area should be opened.
 *
 * @return {Object} Action.
 */

function actions_setIsWidgetAreaOpen(clientId, isOpen) {
  return {
    type: 'SET_IS_WIDGET_AREA_OPEN',
    clientId,
    isOpen
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

function actions_setIsInserterOpened(value) {
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

function actions_setIsListViewOpened(isOpen) {
  return {
    type: 'SET_IS_LIST_VIEW_OPENED',
    isOpen
  };
}
/**
 * Returns an action object signalling that the user closed the sidebar.
 *
 * @return {Object} Action creator.
 */

const actions_closeGeneralSidebar = () => _ref10 => {
  let {
    registry
  } = _ref10;
  registry.dispatch(build_module["i" /* store */]).disableComplementaryArea(STORE_NAME);
};
/**
 * Action that handles moving a block between widget areas
 *
 * @param {string} clientId     The clientId of the block to move.
 * @param {string} widgetAreaId The id of the widget area to move the block to.
 */

const actions_moveBlockToWidgetArea = (clientId, widgetAreaId) => async _ref11 => {
  let {
    dispatch,
    select,
    registry
  } = _ref11;
  const sourceRootClientId = registry.select(external_gc_blockEditor_["store"]).getBlockRootClientId([clientId]); // Search the top level blocks (widget areas) for the one with the matching
  // id attribute. Makes the assumption that all top-level blocks are widget
  // areas.

  const widgetAreas = registry.select(external_gc_blockEditor_["store"]).getBlocks();
  const destinationWidgetAreaBlock = widgetAreas.find(_ref12 => {
    let {
      attributes
    } = _ref12;
    return attributes.id === widgetAreaId;
  });
  const destinationRootClientId = destinationWidgetAreaBlock.clientId; // Get the index for moving to the end of the the destination widget area.

  const destinationInnerBlocksClientIds = registry.select(external_gc_blockEditor_["store"]).getBlockOrder(destinationRootClientId);
  const destinationIndex = destinationInnerBlocksClientIds.length; // Reveal the widget area, if it's not open.

  const isDestinationWidgetAreaOpen = select.getIsWidgetAreaOpen(destinationRootClientId);

  if (!isDestinationWidgetAreaOpen) {
    dispatch.setIsWidgetAreaOpen(destinationRootClientId, true);
  } // Move the block.


  registry.dispatch(external_gc_blockEditor_["store"]).moveBlocksToPosition([clientId], sourceRootClientId, destinationRootClientId, destinationIndex);
};

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/resolvers.js
/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */




/**
 * Creates a "stub" widgets post reflecting all available widget areas. The
 * post is meant as a convenient to only exists in runtime and should never be saved. It
 * enables a convenient way of editing the widgets by using a regular post editor.
 *
 * Fetches all widgets from all widgets aras, converts them into blocks, and hydrates a new post with them.
 *
 * @return {Function} An action creator.
 */

const getWidgetAreas = () => async _ref => {
  let {
    dispatch,
    registry
  } = _ref;
  const query = buildWidgetAreasQuery();
  const widgetAreas = await registry.resolveSelect(external_gc_coreData_["store"]).getEntityRecords(KIND, WIDGET_AREA_ENTITY_TYPE, query);
  const widgetAreaBlocks = [];
  const sortedWidgetAreas = widgetAreas.sort((a, b) => {
    if (a.id === 'gc_inactive_widgets') {
      return 1;
    }

    if (b.id === 'gc_inactive_widgets') {
      return -1;
    }

    return 0;
  });

  for (const widgetArea of sortedWidgetAreas) {
    widgetAreaBlocks.push(Object(external_gc_blocks_["createBlock"])('core/widget-area', {
      id: widgetArea.id,
      name: widgetArea.name
    }));

    if (!widgetArea.widgets.length) {
      // If this widget area has no widgets, it won't get a post setup by
      // the getWidgets resolver.
      dispatch(persistStubPost(buildWidgetAreaPostId(widgetArea.id), []));
    }
  }

  const widgetAreasOpenState = {};
  widgetAreaBlocks.forEach((widgetAreaBlock, index) => {
    // Defaults to open the first widget area.
    widgetAreasOpenState[widgetAreaBlock.clientId] = index === 0;
  });
  dispatch(setWidgetAreasOpenState(widgetAreasOpenState));
  dispatch(persistStubPost(buildWidgetAreasPostId(), widgetAreaBlocks));
};
/**
 * Fetches all widgets from all widgets ares, and groups them by widget area Id.
 *
 * @return {Function} An action creator.
 */

const getWidgets = () => async _ref2 => {
  let {
    dispatch,
    registry
  } = _ref2;
  const query = buildWidgetsQuery();
  const widgets = await registry.resolveSelect(external_gc_coreData_["store"]).getEntityRecords('root', 'widget', query);
  const groupedBySidebar = {};

  for (const widget of widgets) {
    const block = transformWidgetToBlock(widget);
    groupedBySidebar[widget.sidebar] = groupedBySidebar[widget.sidebar] || [];
    groupedBySidebar[widget.sidebar].push(block);
  }

  for (const sidebarId in groupedBySidebar) {
    if (groupedBySidebar.hasOwnProperty(sidebarId)) {
      // Persist the actual post containing the widget block
      dispatch(persistStubPost(buildWidgetAreaPostId(sidebarId), groupedBySidebar[sidebarId]));
    }
  }
};

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/selectors.js
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
 * Returns all API widgets.
 *
 * @return {Object[]} API List of widgets.
 */

const selectors_getWidgets = Object(external_gc_data_["createRegistrySelector"])(select => () => {
  const widgets = select(external_gc_coreData_["store"]).getEntityRecords('root', 'widget', buildWidgetsQuery());
  return Object(external_lodash_["keyBy"])(widgets, 'id');
});
/**
 * Returns API widget data for a particular widget ID.
 *
 * @param {number} id Widget ID.
 *
 * @return {Object} API widget data for a particular widget ID.
 */

const getWidget = Object(external_gc_data_["createRegistrySelector"])(select => (state, id) => {
  const widgets = select(STORE_NAME).getWidgets();
  return widgets[id];
});
/**
 * Returns all API widget areas.
 *
 * @return {Object[]} API List of widget areas.
 */

const selectors_getWidgetAreas = Object(external_gc_data_["createRegistrySelector"])(select => () => {
  const query = buildWidgetAreasQuery();
  return select(external_gc_coreData_["store"]).getEntityRecords(KIND, WIDGET_AREA_ENTITY_TYPE, query);
});
/**
 * Returns widgetArea containing a block identify by given widgetId
 *
 * @param {string} widgetId The ID of the widget.
 * @return {Object} Containing widget area.
 */

const getWidgetAreaForWidgetId = Object(external_gc_data_["createRegistrySelector"])(select => (state, widgetId) => {
  const widgetAreas = select(STORE_NAME).getWidgetAreas();
  return widgetAreas.find(widgetArea => {
    const post = select(external_gc_coreData_["store"]).getEditedEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(widgetArea.id));
    const blockWidgetIds = post.blocks.map(block => Object(external_gc_widgets_["getWidgetIdFromBlock"])(block));
    return blockWidgetIds.includes(widgetId);
  });
});
/**
 * Given a child client id, returns the parent widget area block.
 *
 * @param {string} clientId The client id of a block in a widget area.
 *
 * @return {GCBlock} The widget area block.
 */

const selectors_getParentWidgetAreaBlock = Object(external_gc_data_["createRegistrySelector"])(select => (state, clientId) => {
  const {
    getBlock,
    getBlockName,
    getBlockParents
  } = select(external_gc_blockEditor_["store"]);
  const blockParents = getBlockParents(clientId);
  const widgetAreaClientId = blockParents.find(parentClientId => getBlockName(parentClientId) === 'core/widget-area');
  return getBlock(widgetAreaClientId);
});
/**
 * Returns all edited widget area entity records.
 *
 * @return {Object[]} List of edited widget area entity records.
 */

const selectors_getEditedWidgetAreas = Object(external_gc_data_["createRegistrySelector"])(select => (state, ids) => {
  let widgetAreas = select(STORE_NAME).getWidgetAreas();

  if (!widgetAreas) {
    return [];
  }

  if (ids) {
    widgetAreas = widgetAreas.filter(_ref => {
      let {
        id
      } = _ref;
      return ids.includes(id);
    });
  }

  return widgetAreas.filter(_ref2 => {
    let {
      id
    } = _ref2;
    return select(external_gc_coreData_["store"]).hasEditsForEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(id));
  }).map(_ref3 => {
    let {
      id
    } = _ref3;
    return select(external_gc_coreData_["store"]).getEditedEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, id);
  });
});
/**
 * Returns all blocks representing reference widgets.
 *
 * @param {string} referenceWidgetName Optional. If given, only reference widgets with this name will be returned.
 * @return {Array}  List of all blocks representing reference widgets
 */

const getReferenceWidgetBlocks = Object(external_gc_data_["createRegistrySelector"])(select => function (state) {
  let referenceWidgetName = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  const results = [];
  const widgetAreas = select(STORE_NAME).getWidgetAreas();

  for (const _widgetArea of widgetAreas) {
    const post = select(external_gc_coreData_["store"]).getEditedEntityRecord(KIND, POST_TYPE, buildWidgetAreaPostId(_widgetArea.id));

    for (const block of post.blocks) {
      var _block$attributes;

      if (block.name === 'core/legacy-widget' && (!referenceWidgetName || ((_block$attributes = block.attributes) === null || _block$attributes === void 0 ? void 0 : _block$attributes.referenceWidgetName) === referenceWidgetName)) {
        results.push(block);
      }
    }
  }

  return results;
});
/**
 * Returns true if any widget area is currently being saved.
 *
 * @return {boolean} True if any widget area is currently being saved. False otherwise.
 */

const selectors_isSavingWidgetAreas = Object(external_gc_data_["createRegistrySelector"])(select => () => {
  var _select$getWidgetArea;

  const widgetAreasIds = (_select$getWidgetArea = select(STORE_NAME).getWidgetAreas()) === null || _select$getWidgetArea === void 0 ? void 0 : _select$getWidgetArea.map(_ref4 => {
    let {
      id
    } = _ref4;
    return id;
  });

  if (!widgetAreasIds) {
    return false;
  }

  for (const id of widgetAreasIds) {
    const isSaving = select(external_gc_coreData_["store"]).isSavingEntityRecord(KIND, WIDGET_AREA_ENTITY_TYPE, id);

    if (isSaving) {
      return true;
    }
  }

  const widgetIds = [...Object.keys(select(STORE_NAME).getWidgets()), undefined // account for new widgets without an ID
  ];

  for (const id of widgetIds) {
    const isSaving = select(external_gc_coreData_["store"]).isSavingEntityRecord('root', 'widget', id);

    if (isSaving) {
      return true;
    }
  }

  return false;
});
/**
 * Gets whether the widget area is opened.
 *
 * @param {Array}  state    The open state of the widget areas.
 * @param {string} clientId The clientId of the widget area.
 *
 * @return {boolean} True if the widget area is open.
 */

const getIsWidgetAreaOpen = (state, clientId) => {
  const {
    widgetAreasOpenState
  } = state;
  return !!widgetAreasOpenState[clientId];
};
/**
 * Returns true if the inserter is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the inserter is opened.
 */

function selectors_isInserterOpened(state) {
  return !!state.blockInserterPanel;
}
/**
 * Get the insertion point for the inserter.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID and index to insert at.
 */

function __experimentalGetInsertionPoint(state) {
  const {
    rootClientId,
    insertionIndex
  } = state.blockInserterPanel;
  return {
    rootClientId,
    insertionIndex
  };
}
/**
 * Returns true if a block can be inserted into a widget area.
 *
 * @param {Array}  state     The open state of the widget areas.
 * @param {string} blockName The name of the block being inserted.
 *
 * @return {boolean} True if the block can be inserted in a widget area.
 */

const selectors_canInsertBlockInWidgetArea = Object(external_gc_data_["createRegistrySelector"])(select => (state, blockName) => {
  // Widget areas are always top-level blocks, which getBlocks will return.
  const widgetAreas = select(external_gc_blockEditor_["store"]).getBlocks(); // Makes an assumption that a block that can be inserted into one
  // widget area can be inserted into any widget area. Uses the first
  // widget area for testing whether the block can be inserted.

  const [firstWidgetArea] = widgetAreas;
  return select(external_gc_blockEditor_["store"]).canInsertBlockType(blockName, firstWidgetArea.clientId);
});
/**
 * Returns true if the list view is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the list view is opened.
 */

function selectors_isListViewOpened(state) {
  return state.listViewPanel;
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/store/index.js
/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */






/**
 * Block editor data store configuration.
 *
 * @see https://github.com/GeChiUI/gutenberg/blob/HEAD/packages/data/README.md#registerStore
 *
 * @type {Object}
 */

const storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  resolvers: resolvers_namespaceObject,
  actions: actions_namespaceObject,
  __experimentalUseThunks: true
};
/**
 * Store definition for the edit widgets namespace.
 *
 * @see https://github.com/GeChiUI/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_gc_data_["createReduxStore"])(STORE_NAME, storeConfig); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

Object(external_gc_data_["registerStore"])(STORE_NAME, storeConfig); // This package uses a few in-memory post types as wrappers for convenience.
// This middleware prevents any network requests related to these types as they are
// bound to fail anyway.

external_gc_apiFetch_default.a.use(function (options, next) {
  var _options$path;

  if (((_options$path = options.path) === null || _options$path === void 0 ? void 0 : _options$path.indexOf('/gc/v2/types/widget-area')) === 0) {
    return Promise.resolve({});
  }

  return next(options);
});

// EXTERNAL MODULE: external ["gc","compose"]
var external_gc_compose_ = __webpack_require__("dMTb");

// EXTERNAL MODULE: external ["gc","hooks"]
var external_gc_hooks_ = __webpack_require__("kZIl");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/filters/move-to-widget-area.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


const withMoveToWidgetAreaToolbarItem = Object(external_gc_compose_["createHigherOrderComponent"])(BlockEdit => props => {
  const {
    clientId,
    name: blockName
  } = props;
  const {
    widgetAreas,
    currentWidgetAreaId,
    canInsertBlockInWidgetArea
  } = Object(external_gc_data_["useSelect"])(select => {
    var _widgetAreaBlock$attr;

    // Component won't display for a widget area, so don't run selectors.
    if (blockName === 'core/widget-area') {
      return {};
    }

    const selectors = select(store);
    const widgetAreaBlock = selectors.getParentWidgetAreaBlock(clientId);
    return {
      widgetAreas: selectors.getWidgetAreas(),
      currentWidgetAreaId: widgetAreaBlock === null || widgetAreaBlock === void 0 ? void 0 : (_widgetAreaBlock$attr = widgetAreaBlock.attributes) === null || _widgetAreaBlock$attr === void 0 ? void 0 : _widgetAreaBlock$attr.id,
      canInsertBlockInWidgetArea: selectors.canInsertBlockInWidgetArea(blockName)
    };
  }, [clientId, blockName]);
  const {
    moveBlockToWidgetArea
  } = Object(external_gc_data_["useDispatch"])(store);
  const hasMultipleWidgetAreas = (widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.length) > 1;
  const isMoveToWidgetAreaVisible = blockName !== 'core/widget-area' && hasMultipleWidgetAreas && canInsertBlockInWidgetArea;
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(BlockEdit, props), isMoveToWidgetAreaVisible && Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockControls"], null, Object(external_gc_element_["createElement"])(external_gc_widgets_["MoveToWidgetArea"], {
    widgetAreas: widgetAreas,
    currentWidgetAreaId: currentWidgetAreaId,
    onSelect: widgetAreaId => {
      moveBlockToWidgetArea(props.clientId, widgetAreaId);
    }
  })));
}, 'withMoveToWidgetAreaToolbarItem');
Object(external_gc_hooks_["addFilter"])('editor.BlockEdit', 'core/edit-widgets/block-edit', withMoveToWidgetAreaToolbarItem);

// EXTERNAL MODULE: external ["gc","mediaUtils"]
var external_gc_mediaUtils_ = __webpack_require__("Yjv4");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/filters/replace-media-upload.js
/**
 * GeChiUI dependencies
 */



const replaceMediaUpload = () => external_gc_mediaUtils_["MediaUpload"];

Object(external_gc_hooks_["addFilter"])('editor.MediaUpload', 'core/edit-widgets/replace-media-upload', replaceMediaUpload);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/filters/index.js
/**
 * Internal dependencies
 */



// EXTERNAL MODULE: external ["gc","components"]
var external_gc_components_ = __webpack_require__("jd0n");

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/blocks/widget-area/edit/use-is-dragging-within.js
/**
 * GeChiUI dependencies
 */

/** @typedef {import('@gechiui/element').RefObject} RefObject */

/**
 * A React hook to determine if it's dragging within the target element.
 *
 * @param {RefObject<HTMLElement>} elementRef The target elementRef object.
 *
 * @return {boolean} Is dragging within the target element.
 */

const useIsDraggingWithin = elementRef => {
  const [isDraggingWithin, setIsDraggingWithin] = Object(external_gc_element_["useState"])(false);
  Object(external_gc_element_["useEffect"])(() => {
    const {
      ownerDocument
    } = elementRef.current;

    function handleDragStart(event) {
      // Check the first time when the dragging starts.
      handleDragEnter(event);
    } // Set to false whenever the user cancel the drag event by either releasing the mouse or press Escape.


    function handleDragEnd() {
      setIsDraggingWithin(false);
    }

    function handleDragEnter(event) {
      // Check if the current target is inside the item element.
      if (elementRef.current.contains(event.target)) {
        setIsDraggingWithin(true);
      } else {
        setIsDraggingWithin(false);
      }
    } // Bind these events to the document to catch all drag events.
    // Ideally, we can also use `event.relatedTarget`, but sadly that doesn't work in Safari.


    ownerDocument.addEventListener('dragstart', handleDragStart);
    ownerDocument.addEventListener('dragend', handleDragEnd);
    ownerDocument.addEventListener('dragenter', handleDragEnter);
    return () => {
      ownerDocument.removeEventListener('dragstart', handleDragStart);
      ownerDocument.removeEventListener('dragend', handleDragEnd);
      ownerDocument.removeEventListener('dragenter', handleDragEnter);
    };
  }, []);
  return isDraggingWithin;
};

/* harmony default export */ var use_is_dragging_within = (useIsDraggingWithin);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/blocks/widget-area/edit/inner-blocks.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */


function WidgetAreaInnerBlocks(_ref) {
  let {
    id
  } = _ref;
  const [blocks, onInput, onChange] = Object(external_gc_coreData_["useEntityBlockEditor"])('root', 'postType');
  const innerBlocksRef = Object(external_gc_element_["useRef"])();
  const isDraggingWithinInnerBlocks = use_is_dragging_within(innerBlocksRef);
  const shouldHighlightDropZone = isDraggingWithinInnerBlocks; // Using the experimental hook so that we can control the className of the element.

  const innerBlocksProps = Object(external_gc_blockEditor_["useInnerBlocksProps"])({
    ref: innerBlocksRef
  }, {
    value: blocks,
    onInput,
    onChange,
    templateLock: false,
    renderAppender: external_gc_blockEditor_["InnerBlocks"].ButtonBlockAppender
  });
  return Object(external_gc_element_["createElement"])("div", {
    "data-widget-area-id": id,
    className: classnames_default()('gc-block-widget-area__inner-blocks block-editor-inner-blocks editor-styles-wrapper', {
      'gc-block-widget-area__highlight-drop-zone': shouldHighlightDropZone
    })
  }, Object(external_gc_element_["createElement"])("div", innerBlocksProps));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/blocks/widget-area/edit/index.js


/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */




/** @typedef {import('@gechiui/element').RefObject} RefObject */

function WidgetAreaEdit(_ref) {
  let {
    clientId,
    className,
    attributes: {
      id,
      name
    }
  } = _ref;
  const isOpen = Object(external_gc_data_["useSelect"])(select => select(store).getIsWidgetAreaOpen(clientId), [clientId]);
  const {
    setIsWidgetAreaOpen
  } = Object(external_gc_data_["useDispatch"])(store);
  const wrapper = Object(external_gc_element_["useRef"])();
  const setOpen = Object(external_gc_element_["useCallback"])(openState => setIsWidgetAreaOpen(clientId, openState), [clientId]);
  const isDragging = useIsDragging(wrapper);
  const isDraggingWithin = use_is_dragging_within(wrapper);
  const [openedWhileDragging, setOpenedWhileDragging] = Object(external_gc_element_["useState"])(false);
  Object(external_gc_element_["useEffect"])(() => {
    if (!isDragging) {
      setOpenedWhileDragging(false);
      return;
    }

    if (isDraggingWithin && !isOpen) {
      setOpen(true);
      setOpenedWhileDragging(true);
    } else if (!isDraggingWithin && isOpen && openedWhileDragging) {
      setOpen(false);
    }
  }, [isOpen, isDragging, isDraggingWithin, openedWhileDragging]);
  return Object(external_gc_element_["createElement"])(external_gc_components_["Panel"], {
    className: className,
    ref: wrapper
  }, Object(external_gc_element_["createElement"])(external_gc_components_["PanelBody"], {
    title: name,
    opened: isOpen,
    onToggle: () => {
      setIsWidgetAreaOpen(clientId, !isOpen);
    },
    scrollAfterOpen: !isDragging
  }, _ref2 => {
    let {
      opened
    } = _ref2;
    return (// This is required to ensure LegacyWidget blocks are not
      // unmounted when the panel is collapsed. Unmounting legacy
      // widgets may have unintended consequences (e.g.  TinyMCE
      // not being properly reinitialized)
      Object(external_gc_element_["createElement"])(external_gc_components_["__unstableDisclosureContent"], {
        className: "gc-block-widget-area__panel-body-content",
        visible: opened
      }, Object(external_gc_element_["createElement"])(external_gc_coreData_["EntityProvider"], {
        kind: "root",
        type: "postType",
        id: `widget-area-${id}`
      }, Object(external_gc_element_["createElement"])(WidgetAreaInnerBlocks, {
        id: id
      })))
    );
  }));
}
/**
 * A React hook to determine if dragging is active.
 *
 * @param {RefObject<HTMLElement>} elementRef The target elementRef object.
 *
 * @return {boolean} Is dragging within the entire document.
 */

const useIsDragging = elementRef => {
  const [isDragging, setIsDragging] = Object(external_gc_element_["useState"])(false);
  Object(external_gc_element_["useEffect"])(() => {
    const {
      ownerDocument
    } = elementRef.current;

    function handleDragStart() {
      setIsDragging(true);
    }

    function handleDragEnd() {
      setIsDragging(false);
    }

    ownerDocument.addEventListener('dragstart', handleDragStart);
    ownerDocument.addEventListener('dragend', handleDragEnd);
    return () => {
      ownerDocument.removeEventListener('dragstart', handleDragStart);
      ownerDocument.removeEventListener('dragend', handleDragEnd);
    };
  }, []);
  return isDragging;
};

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/blocks/widget-area/index.js
/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */

const widget_area_metadata = {
  name: "core/widget-area",
  category: "widgets",
  attributes: {
    id: {
      type: "string"
    },
    name: {
      type: "string"
    }
  },
  supports: {
    html: false,
    inserter: false,
    customClassName: false,
    reusable: false,
    __experimentalToolbar: false,
    __experimentalParentSelector: false
  },
  editorStyle: "gc-block-widget-area-editor",
  style: "gc-block-widget-area"
};

const {
  name: widget_area_name
} = widget_area_metadata;

const widget_area_settings = {
  title: Object(external_gc_i18n_["__"])('小工具区域'),
  description: Object(external_gc_i18n_["__"])('小工具区域容器。'),
  __experimentalLabel: _ref => {
    let {
      name: label
    } = _ref;
    return label;
  },
  edit: WidgetAreaEdit
};

// EXTERNAL MODULE: external ["gc","plugins"]
var external_gc_plugins_ = __webpack_require__("Xm27");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/error-boundary/index.js


/**
 * GeChiUI dependencies
 */






function CopyButton(_ref) {
  let {
    text,
    children
  } = _ref;
  const ref = Object(external_gc_compose_["useCopyToClipboard"])(text);
  return Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
    variant: "secondary",
    ref: ref
  }, children);
}

class error_boundary_ErrorBoundary extends external_gc_element_["Component"] {
  constructor() {
    super(...arguments);
    this.reboot = this.reboot.bind(this);
    this.state = {
      error: null
    };
  }

  componentDidCatch(error) {
    this.setState({
      error
    });
  }

  reboot() {
    this.props.onError();
  }

  render() {
    const {
      error
    } = this.state;

    if (!error) {
      return this.props.children;
    }

    return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["Warning"], {
      className: "edit-widgets-error-boundary",
      actions: [Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
        key: "recovery",
        onClick: this.reboot,
        variant: "secondary"
      }, Object(external_gc_i18n_["__"])('尝试恢复')), Object(external_gc_element_["createElement"])(CopyButton, {
        key: "copy-error",
        text: error.stack
      }, Object(external_gc_i18n_["__"])('复制错误'))]
    }, Object(external_gc_i18n_["__"])('编辑器遇到了未知错误。'));
  }

}

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["gc","reusableBlocks"]
var external_gc_reusableBlocks_ = __webpack_require__("Ppya");

// EXTERNAL MODULE: external ["gc","keyboardShortcuts"]
var external_gc_keyboardShortcuts_ = __webpack_require__("IuOC");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/keyboard-shortcuts/index.js
/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */



function KeyboardShortcuts() {
  const {
    redo,
    undo
  } = Object(external_gc_data_["useDispatch"])(external_gc_coreData_["store"]);
  const {
    saveEditedWidgetAreas
  } = Object(external_gc_data_["useDispatch"])(store);
  Object(external_gc_keyboardShortcuts_["useShortcut"])('core/edit-widgets/undo', event => {
    undo();
    event.preventDefault();
  });
  Object(external_gc_keyboardShortcuts_["useShortcut"])('core/edit-widgets/redo', event => {
    redo();
    event.preventDefault();
  });
  Object(external_gc_keyboardShortcuts_["useShortcut"])('core/edit-widgets/save', event => {
    event.preventDefault();
    saveEditedWidgetAreas();
  });
  return null;
}

function KeyboardShortcutsRegister() {
  // Registering the shortcuts
  const {
    registerShortcut
  } = Object(external_gc_data_["useDispatch"])(external_gc_keyboardShortcuts_["store"]);
  Object(external_gc_element_["useEffect"])(() => {
    registerShortcut({
      name: 'core/edit-widgets/undo',
      category: 'global',
      description: Object(external_gc_i18n_["__"])('撤销您的上一个更改。'),
      keyCombination: {
        modifier: 'primary',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/edit-widgets/redo',
      category: 'global',
      description: Object(external_gc_i18n_["__"])('重做您撤销的操作。'),
      keyCombination: {
        modifier: 'primaryShift',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/edit-widgets/save',
      category: 'global',
      description: Object(external_gc_i18n_["__"])('保存您的更改。'),
      keyCombination: {
        modifier: 'primary',
        character: 's'
      }
    });
    registerShortcut({
      name: 'core/edit-widgets/keyboard-shortcuts',
      category: 'main',
      description: Object(external_gc_i18n_["__"])('显示这些键盘快捷键。'),
      keyCombination: {
        modifier: 'access',
        character: 'h'
      }
    });
    registerShortcut({
      name: 'core/edit-widgets/next-region',
      category: 'global',
      description: Object(external_gc_i18n_["__"])('导航至编辑器的下一个功能区域。'),
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
      name: 'core/edit-widgets/previous-region',
      category: 'global',
      description: Object(external_gc_i18n_["__"])('导航至编辑器的上一个功能区域。'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }]
    });
  }, [registerShortcut]);
  return null;
}

KeyboardShortcuts.Register = KeyboardShortcutsRegister;
/* harmony default export */ var keyboard_shortcuts = (KeyboardShortcuts);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/hooks/use-last-selected-widget-area.js
/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */



/**
 * A react hook that returns the client id of the last widget area to have
 * been selected, or to have a selected block within it.
 *
 * @return {string} clientId of the widget area last selected.
 */

const useLastSelectedWidgetArea = () => Object(external_gc_data_["useSelect"])(select => {
  var _widgetAreasPost$bloc;

  const {
    getBlockSelectionEnd,
    getBlockName
  } = select(external_gc_blockEditor_["store"]);
  const selectionEndClientId = getBlockSelectionEnd(); // If the selected block is a widget area, return its clientId.

  if (getBlockName(selectionEndClientId) === 'core/widget-area') {
    return selectionEndClientId;
  }

  const {
    getParentWidgetAreaBlock
  } = select(store);
  const widgetAreaBlock = getParentWidgetAreaBlock(selectionEndClientId);
  const widgetAreaBlockClientId = widgetAreaBlock === null || widgetAreaBlock === void 0 ? void 0 : widgetAreaBlock.clientId;

  if (widgetAreaBlockClientId) {
    return widgetAreaBlockClientId;
  } // If no widget area has been selected, return the clientId of the first
  // area.


  const {
    getEntityRecord
  } = select(external_gc_coreData_["store"]);
  const widgetAreasPost = getEntityRecord(KIND, POST_TYPE, buildWidgetAreasPostId());
  return widgetAreasPost === null || widgetAreasPost === void 0 ? void 0 : (_widgetAreasPost$bloc = widgetAreasPost.blocks[0]) === null || _widgetAreasPost$bloc === void 0 ? void 0 : _widgetAreasPost$bloc.clientId;
}, []);

/* harmony default export */ var use_last_selected_widget_area = (useLastSelectedWidgetArea);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/constants.js
const ALLOW_REUSABLE_BLOCKS = false;
const ENABLE_EXPERIMENTAL_FSE_BLOCKS = false;

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/widget-areas-block-editor-provider/index.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */









/**
 * Internal dependencies
 */







function WidgetAreasBlockEditorProvider(_ref) {
  let {
    blockEditorSettings,
    children,
    ...props
  } = _ref;
  const {
    hasUploadPermissions,
    reusableBlocks,
    isFixedToolbarActive,
    keepCaretInsideBlock
  } = Object(external_gc_data_["useSelect"])(select => ({
    hasUploadPermissions: Object(external_lodash_["defaultTo"])(select(external_gc_coreData_["store"]).canUser('create', 'media'), true),
    widgetAreas: select(store).getWidgetAreas(),
    widgets: select(store).getWidgets(),
    reusableBlocks: ALLOW_REUSABLE_BLOCKS ? select(external_gc_coreData_["store"]).getEntityRecords('postType', 'gc_block') : [],
    isFixedToolbarActive: select(build_module["i" /* store */]).isFeatureActive('core/edit-widgets', 'fixedToolbar'),
    keepCaretInsideBlock: select(build_module["i" /* store */]).isFeatureActive('core/edit-widgets', 'keepCaretInsideBlock')
  }), []);
  const {
    setIsInserterOpened
  } = Object(external_gc_data_["useDispatch"])(store);
  const settings = Object(external_gc_element_["useMemo"])(() => {
    let mediaUploadBlockEditor;

    if (hasUploadPermissions) {
      mediaUploadBlockEditor = _ref2 => {
        let {
          onError,
          ...argumentsObject
        } = _ref2;
        Object(external_gc_mediaUtils_["uploadMedia"])({
          gcAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
          onError: _ref3 => {
            let {
              message
            } = _ref3;
            return onError(message);
          },
          ...argumentsObject
        });
      };
    }

    return { ...blockEditorSettings,
      __experimentalReusableBlocks: reusableBlocks,
      hasFixedToolbar: isFixedToolbarActive,
      keepCaretInsideBlock,
      mediaUpload: mediaUploadBlockEditor,
      templateLock: 'all',
      __experimentalSetIsInserterOpened: setIsInserterOpened
    };
  }, [blockEditorSettings, isFixedToolbarActive, keepCaretInsideBlock, hasUploadPermissions, reusableBlocks, setIsInserterOpened]);
  const widgetAreaId = use_last_selected_widget_area();
  const [blocks, onInput, onChange] = Object(external_gc_coreData_["useEntityBlockEditor"])(KIND, POST_TYPE, {
    id: buildWidgetAreasPostId()
  });
  return Object(external_gc_element_["createElement"])(external_gc_keyboardShortcuts_["ShortcutProvider"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockEditorKeyboardShortcuts"].Register, null), Object(external_gc_element_["createElement"])(keyboard_shortcuts.Register, null), Object(external_gc_element_["createElement"])(external_gc_components_["SlotFillProvider"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockEditorProvider"], Object(esm_extends["a" /* default */])({
    value: blocks,
    onInput: onInput,
    onChange: onChange,
    settings: settings,
    useSubRegistry: false
  }, props), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["CopyHandler"], null, children), Object(external_gc_element_["createElement"])(external_gc_reusableBlocks_["ReusableBlocksMenuItems"], {
    rootClientId: widgetAreaId
  }))));
}

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/cog.js
var cog = __webpack_require__("v/OI");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/block-default.js
var block_default = __webpack_require__("0GdS");

// EXTERNAL MODULE: external ["gc","url"]
var external_gc_url_ = __webpack_require__("zP/e");

// EXTERNAL MODULE: external ["gc","dom"]
var external_gc_dom_ = __webpack_require__("EXvS");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/sidebar/widget-areas.js


/**
 * GeChiUI dependencies
 */








/**
 * Internal dependencies
 */


function WidgetAreas(_ref) {
  let {
    selectedWidgetAreaId
  } = _ref;
  const widgetAreas = Object(external_gc_data_["useSelect"])(select => select(store).getWidgetAreas(), []);
  const selectedWidgetArea = Object(external_gc_element_["useMemo"])(() => selectedWidgetAreaId && (widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.find(widgetArea => widgetArea.id === selectedWidgetAreaId)), [selectedWidgetAreaId, widgetAreas]);
  let description;

  if (!selectedWidgetArea) {
    description = Object(external_gc_i18n_["__"])('小工具区域是站点布局中可以支持区块的全局部分。这些区域因主题而异，但通常都是您的边栏或页脚部分。');
  } else if (selectedWidgetAreaId === 'gc_inactive_widgets') {
    description = Object(external_gc_i18n_["__"])('此小工具区域中的区块将不会显示在您的站点中。');
  } else {
    description = selectedWidgetArea.description;
  }

  return Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-widget-areas"
  }, Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-widget-areas__top-container"
  }, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockIcon"], {
    icon: block_default["a" /* default */]
  }), Object(external_gc_element_["createElement"])("div", null, Object(external_gc_element_["createElement"])("p", {
    // Use `dangerouslySetInnerHTML` to keep backwards
    // compatibility. Basic markup in the description is an
    // established feature of GeChiUI.
    // @see https://github.com/GeChiUI/gutenberg/issues/33106
    dangerouslySetInnerHTML: {
      __html: Object(external_gc_dom_["safeHTML"])(description)
    }
  }), (widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.length) === 0 && Object(external_gc_element_["createElement"])("p", null, Object(external_gc_i18n_["__"])('您的主题不包含任何小工具区域。')), !selectedWidgetArea && Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
    href: Object(external_gc_url_["addQueryArgs"])('customize.php', {
      'autofocus[panel]': 'widgets',
      return: window.location.pathname
    }),
    variant: "tertiary"
  }, Object(external_gc_i18n_["__"])('使用实时预览进行管理')))));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/sidebar/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */








const SIDEBAR_ACTIVE_BY_DEFAULT = external_gc_element_["Platform"].select({
  web: true,
  native: false
});
const BLOCK_INSPECTOR_IDENTIFIER = 'edit-widgets/block-inspector'; // Widget areas were one called block areas, so use 'edit-widgets/block-areas'
// for backwards compatibility.

const WIDGET_AREAS_IDENTIFIER = 'edit-widgets/block-areas';
/**
 * Internal dependencies
 */




function ComplementaryAreaTab(_ref) {
  let {
    identifier,
    label,
    isActive
  } = _ref;
  const {
    enableComplementaryArea
  } = Object(external_gc_data_["useDispatch"])(build_module["i" /* store */]);
  return Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
    onClick: () => enableComplementaryArea(store.name, identifier),
    className: classnames_default()('edit-widgets-sidebar__panel-tab', {
      'is-active': isActive
    }),
    "aria-label": isActive ? // translators: %s: sidebar label e.g: "Widget Areas".
    Object(external_gc_i18n_["sprintf"])(Object(external_gc_i18n_["__"])('%s（已选）'), label) : label,
    "data-label": label
  }, label);
}

function Sidebar() {
  const {
    enableComplementaryArea
  } = Object(external_gc_data_["useDispatch"])(build_module["i" /* store */]);
  const {
    currentArea,
    hasSelectedNonAreaBlock,
    isGeneralSidebarOpen,
    selectedWidgetAreaBlock
  } = Object(external_gc_data_["useSelect"])(select => {
    const {
      getSelectedBlock,
      getBlock,
      getBlockParentsByBlockName
    } = select(external_gc_blockEditor_["store"]);
    const {
      getActiveComplementaryArea
    } = select(build_module["i" /* store */]);
    const selectedBlock = getSelectedBlock();
    const activeArea = getActiveComplementaryArea(store.name);
    let currentSelection = activeArea;

    if (!currentSelection) {
      if (selectedBlock) {
        currentSelection = BLOCK_INSPECTOR_IDENTIFIER;
      } else {
        currentSelection = WIDGET_AREAS_IDENTIFIER;
      }
    }

    let widgetAreaBlock;

    if (selectedBlock) {
      if (selectedBlock.name === 'core/widget-area') {
        widgetAreaBlock = selectedBlock;
      } else {
        widgetAreaBlock = getBlock(getBlockParentsByBlockName(selectedBlock.clientId, 'core/widget-area')[0]);
      }
    }

    return {
      currentArea: currentSelection,
      hasSelectedNonAreaBlock: !!(selectedBlock && selectedBlock.name !== 'core/widget-area'),
      isGeneralSidebarOpen: !!activeArea,
      selectedWidgetAreaBlock: widgetAreaBlock
    };
  }, []); // currentArea, and isGeneralSidebarOpen are intentionally left out from the dependencies,
  // because we want to run the effect when a block is selected/unselected and not when the sidebar state changes.

  Object(external_gc_element_["useEffect"])(() => {
    if (hasSelectedNonAreaBlock && currentArea === WIDGET_AREAS_IDENTIFIER && isGeneralSidebarOpen) {
      enableComplementaryArea('core/edit-widgets', BLOCK_INSPECTOR_IDENTIFIER);
    }

    if (!hasSelectedNonAreaBlock && currentArea === BLOCK_INSPECTOR_IDENTIFIER && isGeneralSidebarOpen) {
      enableComplementaryArea('core/edit-widgets', WIDGET_AREAS_IDENTIFIER);
    }
  }, [hasSelectedNonAreaBlock, enableComplementaryArea]);
  return Object(external_gc_element_["createElement"])(build_module["b" /* ComplementaryArea */], {
    className: "edit-widgets-sidebar",
    header: Object(external_gc_element_["createElement"])("ul", null, Object(external_gc_element_["createElement"])("li", null, Object(external_gc_element_["createElement"])(ComplementaryAreaTab, {
      identifier: WIDGET_AREAS_IDENTIFIER,
      label: selectedWidgetAreaBlock ? selectedWidgetAreaBlock.attributes.name : Object(external_gc_i18n_["__"])('小工具区域'),
      isActive: currentArea === WIDGET_AREAS_IDENTIFIER
    })), Object(external_gc_element_["createElement"])("li", null, Object(external_gc_element_["createElement"])(ComplementaryAreaTab, {
      identifier: BLOCK_INSPECTOR_IDENTIFIER,
      label: Object(external_gc_i18n_["__"])('区块'),
      isActive: currentArea === BLOCK_INSPECTOR_IDENTIFIER
    }))),
    headerClassName: "edit-widgets-sidebar__panel-tabs"
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    title: Object(external_gc_i18n_["__"])('设置'),
    closeLabel: Object(external_gc_i18n_["__"])('关闭设置'),
    scope: "core/edit-widgets",
    identifier: currentArea,
    icon: cog["a" /* default */],
    isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT
  }, currentArea === WIDGET_AREAS_IDENTIFIER && Object(external_gc_element_["createElement"])(WidgetAreas, {
    selectedWidgetAreaId: selectedWidgetAreaBlock === null || selectedWidgetAreaBlock === void 0 ? void 0 : selectedWidgetAreaBlock.attributes.id
  }), currentArea === BLOCK_INSPECTOR_IDENTIFIER && (hasSelectedNonAreaBlock ? Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockInspector"], null) : // Pretend that Widget Areas are part of the UI by not
  // showing the Block Inspector when one is selected.
  Object(external_gc_element_["createElement"])("span", {
    className: "block-editor-block-inspector__no-blocks"
  }, Object(external_gc_i18n_["__"])('未选择区块。'))));
}

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/plus.js
var plus = __webpack_require__("th/Q");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/list-view.js
var list_view = __webpack_require__("2BQN");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/save-button/index.js


/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */



function SaveButton() {
  const {
    hasEditedWidgetAreaIds,
    isSaving
  } = Object(external_gc_data_["useSelect"])(select => {
    var _getEditedWidgetAreas;

    const {
      getEditedWidgetAreas,
      isSavingWidgetAreas
    } = select(store);
    return {
      hasEditedWidgetAreaIds: ((_getEditedWidgetAreas = getEditedWidgetAreas()) === null || _getEditedWidgetAreas === void 0 ? void 0 : _getEditedWidgetAreas.length) > 0,
      isSaving: isSavingWidgetAreas()
    };
  }, []);
  const {
    saveEditedWidgetAreas
  } = Object(external_gc_data_["useDispatch"])(store);
  return Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
    variant: "primary",
    isBusy: isSaving,
    "aria-disabled": isSaving,
    onClick: isSaving ? undefined : saveEditedWidgetAreas,
    disabled: !hasEditedWidgetAreaIds
  }, isSaving ? Object(external_gc_i18n_["__"])('正在保存…') : Object(external_gc_i18n_["__"])('更新'));
}

/* harmony default export */ var save_button = (SaveButton);

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/undo.js
var library_undo = __webpack_require__("mkk6");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/redo.js
var library_redo = __webpack_require__("WNKN");

// EXTERNAL MODULE: external ["gc","keycodes"]
var external_gc_keycodes_ = __webpack_require__("l35S");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/header/undo-redo/undo.js


/**
 * GeChiUI dependencies
 */






function UndoButton() {
  const hasUndo = Object(external_gc_data_["useSelect"])(select => select(external_gc_coreData_["store"]).hasUndo(), []);
  const {
    undo
  } = Object(external_gc_data_["useDispatch"])(external_gc_coreData_["store"]);
  return Object(external_gc_element_["createElement"])(external_gc_components_["ToolbarButton"], {
    icon: !Object(external_gc_i18n_["isRTL"])() ? library_undo["a" /* default */] : library_redo["a" /* default */],
    label: Object(external_gc_i18n_["__"])('撤销'),
    shortcut: external_gc_keycodes_["displayShortcut"].primary('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/GeChiUI/gutenberg/issues/3486
    ,
    "aria-disabled": !hasUndo,
    onClick: hasUndo ? undo : undefined
  });
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/header/undo-redo/redo.js


/**
 * GeChiUI dependencies
 */






function RedoButton() {
  const hasRedo = Object(external_gc_data_["useSelect"])(select => select(external_gc_coreData_["store"]).hasRedo(), []);
  const {
    redo
  } = Object(external_gc_data_["useDispatch"])(external_gc_coreData_["store"]);
  return Object(external_gc_element_["createElement"])(external_gc_components_["ToolbarButton"], {
    icon: !Object(external_gc_i18n_["isRTL"])() ? library_redo["a" /* default */] : library_undo["a" /* default */],
    label: Object(external_gc_i18n_["__"])('重做'),
    shortcut: external_gc_keycodes_["displayShortcut"].primaryShift('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/GeChiUI/gutenberg/issues/3486
    ,
    "aria-disabled": !hasRedo,
    onClick: hasRedo ? redo : undefined
  });
}

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/external.js
var external = __webpack_require__("rHI3");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * GeChiUI dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: Object(external_gc_i18n_["__"])('将选定的文字加粗。')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: Object(external_gc_i18n_["__"])('将选定的文字设为斜体。')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: Object(external_gc_i18n_["__"])('将选定的文字转换为链接。')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: Object(external_gc_i18n_["__"])('移除链接。')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: Object(external_gc_i18n_["__"])('给选定的文字加下划线。')
}];

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */




function KeyCombination(_ref) {
  let {
    keyCombination,
    forceAriaLabel
  } = _ref;
  const shortcut = keyCombination.modifier ? external_gc_keycodes_["displayShortcutList"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_gc_keycodes_["shortcutAriaLabel"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return Object(external_gc_element_["createElement"])("kbd", {
    className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, Object(external_lodash_["castArray"])(shortcut).map((character, index) => {
    if (character === '+') {
      return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], {
        key: index
      }, character);
    }

    return Object(external_gc_element_["createElement"])("kbd", {
      key: index,
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key"
    }, character);
  }));
}

function Shortcut(_ref2) {
  let {
    description,
    keyCombination,
    aliases = [],
    ariaLabel
  } = _ref2;
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-description"
  }, description), Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-term"
  }, Object(external_gc_element_["createElement"])(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map((alias, index) => Object(external_gc_element_["createElement"])(KeyCombination, {
    keyCombination: alias,
    forceAriaLabel: ariaLabel,
    key: index
  }))));
}

/* harmony default export */ var keyboard_shortcut_help_modal_shortcut = (Shortcut);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */



function DynamicShortcut(_ref) {
  let {
    name
  } = _ref;
  const {
    keyCombination,
    description,
    aliases
  } = Object(external_gc_data_["useSelect"])(select => {
    const {
      getShortcutKeyCombination,
      getShortcutDescription,
      getShortcutAliases
    } = select(external_gc_keyboardShortcuts_["store"]);
    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  }, [name]);

  if (!keyCombination) {
    return null;
  }

  return Object(external_gc_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

/* harmony default export */ var dynamic_shortcut = (DynamicShortcut);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/keyboard-shortcut-help-modal/index.js


/**
 * External dependencies
 */


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */





const ShortcutList = _ref => {
  let {
    shortcuts
  } = _ref;
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    Object(external_gc_element_["createElement"])("ul", {
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-list",
      role: "list"
    }, shortcuts.map((shortcut, index) => Object(external_gc_element_["createElement"])("li", {
      className: "edit-widgets-keyboard-shortcut-help-modal__shortcut",
      key: index
    }, Object(external_lodash_["isString"])(shortcut) ? Object(external_gc_element_["createElement"])(dynamic_shortcut, {
      name: shortcut
    }) : Object(external_gc_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, shortcut))))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
};

const ShortcutSection = _ref2 => {
  let {
    title,
    shortcuts,
    className
  } = _ref2;
  return Object(external_gc_element_["createElement"])("section", {
    className: classnames_default()('edit-widgets-keyboard-shortcut-help-modal__section', className)
  }, !!title && Object(external_gc_element_["createElement"])("h2", {
    className: "edit-widgets-keyboard-shortcut-help-modal__section-title"
  }, title), Object(external_gc_element_["createElement"])(ShortcutList, {
    shortcuts: shortcuts
  }));
};

const ShortcutCategorySection = _ref3 => {
  let {
    title,
    categoryName,
    additionalShortcuts = []
  } = _ref3;
  const categoryShortcuts = Object(external_gc_data_["useSelect"])(select => {
    return select(external_gc_keyboardShortcuts_["store"]).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return Object(external_gc_element_["createElement"])(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal(_ref4) {
  let {
    isModalActive,
    toggleModal
  } = _ref4;
  Object(external_gc_keyboardShortcuts_["useShortcut"])('core/edit-widgets/keyboard-shortcuts', toggleModal, {
    bindGlobal: true
  });

  if (!isModalActive) {
    return null;
  }

  return Object(external_gc_element_["createElement"])(external_gc_components_["Modal"], {
    className: "edit-widgets-keyboard-shortcut-help-modal",
    title: Object(external_gc_i18n_["__"])('键盘快捷键'),
    closeLabel: Object(external_gc_i18n_["__"])('关闭'),
    onRequestClose: toggleModal
  }, Object(external_gc_element_["createElement"])(ShortcutSection, {
    className: "edit-widgets-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/edit-widgets/keyboard-shortcuts']
  }), Object(external_gc_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_gc_i18n_["__"])('全局快捷键'),
    categoryName: "global"
  }), Object(external_gc_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_gc_i18n_["__"])('选定快捷键'),
    categoryName: "selection"
  }), Object(external_gc_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_gc_i18n_["__"])('区块快捷键'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: Object(external_gc_i18n_["__"])('在添加新段落后修改区块类型。'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: Object(external_gc_i18n_["__"])('正斜杠')
    }]
  }), Object(external_gc_element_["createElement"])(ShortcutSection, {
    title: Object(external_gc_i18n_["__"])('文字内容格式化'),
    shortcuts: textFormattingShortcuts
  }));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/more-menu/index.js


/**
 * GeChiUI dependencies
 */








/**
 * Internal dependencies
 */


function MoreMenu() {
  const [isKeyboardShortcutsModalActive, setIsKeyboardShortcutsModalVisible] = Object(external_gc_element_["useState"])(false);

  const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);

  Object(external_gc_keyboardShortcuts_["useShortcut"])('core/edit-widgets/keyboard-shortcuts', toggleKeyboardShortcutsModal);
  const isLargeViewport = Object(external_gc_compose_["useViewportMatch"])('medium');
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(build_module["f" /* MoreMenuDropdown */], null, () => Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, isLargeViewport && Object(external_gc_element_["createElement"])(external_gc_components_["MenuGroup"], {
    label: Object(external_gc_i18n_["_x"])('查看', 'noun')
  }, Object(external_gc_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-widgets",
    feature: "fixedToolbar",
    label: Object(external_gc_i18n_["__"])('顶部工具栏'),
    info: Object(external_gc_i18n_["__"])('在一个位置访问所有的区块和文档工具'),
    messageActivated: Object(external_gc_i18n_["__"])('顶部工具栏已启用'),
    messageDeactivated: Object(external_gc_i18n_["__"])('顶部工具栏已禁用')
  })), Object(external_gc_element_["createElement"])(external_gc_components_["MenuGroup"], {
    label: Object(external_gc_i18n_["__"])('工具')
  }, Object(external_gc_element_["createElement"])(external_gc_components_["MenuItem"], {
    onClick: () => {
      setIsKeyboardShortcutsModalVisible(true);
    },
    shortcut: external_gc_keycodes_["displayShortcut"].access('h')
  }, Object(external_gc_i18n_["__"])('键盘快捷键')), Object(external_gc_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-widgets",
    feature: "welcomeGuide",
    label: Object(external_gc_i18n_["__"])('欢迎指南')
  }), Object(external_gc_element_["createElement"])(external_gc_components_["MenuItem"], {
    role: "menuitem",
    icon: external["a" /* default */],
    href: Object(external_gc_i18n_["__"])('https://www.gechiui.com/support/article/block-based-widgets-editor/'),
    target: "_blank",
    rel: "noopener noreferrer"
  }, Object(external_gc_i18n_["__"])('帮助'), Object(external_gc_element_["createElement"])(external_gc_components_["VisuallyHidden"], {
    as: "span"
  },
  /* translators: accessibility text */
  Object(external_gc_i18n_["__"])('（在新窗口中打开）')))), Object(external_gc_element_["createElement"])(external_gc_components_["MenuGroup"], {
    label: Object(external_gc_i18n_["__"])('偏好设置')
  }, Object(external_gc_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-widgets",
    feature: "keepCaretInsideBlock",
    label: Object(external_gc_i18n_["__"])('在区块内包含文本光标'),
    info: Object(external_gc_i18n_["__"])('通过阻止光标离开区块来帮助屏幕阅读器。'),
    messageActivated: Object(external_gc_i18n_["__"])('启用区块内的文本光标'),
    messageDeactivated: Object(external_gc_i18n_["__"])('禁用区块内的文本光标')
  }), Object(external_gc_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-widgets",
    feature: "themeStyles",
    info: Object(external_gc_i18n_["__"])('使编辑器看起来像您的主题。'),
    label: Object(external_gc_i18n_["__"])('使用主题样式')
  }), isLargeViewport && Object(external_gc_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-widgets",
    feature: "showBlockBreadcrumbs",
    label: Object(external_gc_i18n_["__"])('显示区块面包屑'),
    info: Object(external_gc_i18n_["__"])('在编辑器的底部显示区块的面包屑。'),
    messageActivated: Object(external_gc_i18n_["__"])('显示区块面包屑已启用'),
    messageDeactivated: Object(external_gc_i18n_["__"])('显示区块面包屑已禁用')
  })))), Object(external_gc_element_["createElement"])(KeyboardShortcutHelpModal, {
    isModalActive: isKeyboardShortcutsModalActive,
    toggleModal: toggleKeyboardShortcutsModal
  }));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/header/index.js


/**
 * GeChiUI dependencies
 */








/**
 * Internal dependencies
 */








function Header() {
  const isMediumViewport = Object(external_gc_compose_["useViewportMatch"])('medium');
  const inserterButton = Object(external_gc_element_["useRef"])();
  const widgetAreaClientId = use_last_selected_widget_area();
  const isLastSelectedWidgetAreaOpen = Object(external_gc_data_["useSelect"])(select => select(store).getIsWidgetAreaOpen(widgetAreaClientId), [widgetAreaClientId]);
  const {
    isInserterOpen,
    isListViewOpen
  } = Object(external_gc_data_["useSelect"])(select => {
    const {
      isInserterOpened,
      isListViewOpened
    } = select(store);
    return {
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened()
    };
  }, []);
  const {
    setIsWidgetAreaOpen,
    setIsInserterOpened,
    setIsListViewOpened
  } = Object(external_gc_data_["useDispatch"])(store);
  const {
    selectBlock
  } = Object(external_gc_data_["useDispatch"])(external_gc_blockEditor_["store"]);

  const handleClick = () => {
    if (isInserterOpen) {
      // Focusing the inserter button closes the inserter popover
      setIsInserterOpened(false);
    } else {
      if (!isLastSelectedWidgetAreaOpen) {
        // Select the last selected block if hasn't already.
        selectBlock(widgetAreaClientId); // Open the last selected widget area when opening the inserter.

        setIsWidgetAreaOpen(widgetAreaClientId, true);
      } // The DOM updates resulting from selectBlock() and setIsInserterOpened() calls are applied the
      // same tick and pretty much in a random order. The inserter is closed if any other part of the
      // app receives focus. If selectBlock() happens to take effect after setIsInserterOpened() then
      // the inserter is visible for a brief moment and then gets auto-closed due to focus moving to
      // the selected block.


      window.requestAnimationFrame(() => setIsInserterOpened(true));
    }
  };

  const toggleListView = Object(external_gc_element_["useCallback"])(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-header"
  }, Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-header__navigable-toolbar-wrapper"
  }, isMediumViewport && Object(external_gc_element_["createElement"])("h1", {
    className: "edit-widgets-header__title"
  }, Object(external_gc_i18n_["__"])('小工具')), !isMediumViewport && Object(external_gc_element_["createElement"])(external_gc_components_["VisuallyHidden"], {
    as: "h1",
    className: "edit-widgets-header__title"
  }, Object(external_gc_i18n_["__"])('小工具')), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["NavigableToolbar"], {
    className: "edit-widgets-header-toolbar",
    "aria-label": Object(external_gc_i18n_["__"])('文档工具')
  }, Object(external_gc_element_["createElement"])(external_gc_components_["ToolbarItem"], {
    ref: inserterButton,
    as: external_gc_components_["Button"],
    className: "edit-widgets-header-toolbar__inserter-toggle",
    variant: "primary",
    isPressed: isInserterOpen,
    onMouseDown: event => {
      event.preventDefault();
    },
    onClick: handleClick,
    icon: plus["a" /* default */]
    /* translators: button label text should, if possible, be under 16
    characters. */
    ,
    label: Object(external_gc_i18n_["_x"])('切换区块插入器', 'Generic label for block inserter button')
  }), isMediumViewport && Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(UndoButton, null), Object(external_gc_element_["createElement"])(RedoButton, null), Object(external_gc_element_["createElement"])(external_gc_components_["ToolbarItem"], {
    as: external_gc_components_["Button"],
    className: "edit-widgets-header-toolbar__list-view-toggle",
    icon: list_view["a" /* default */],
    isPressed: isListViewOpen
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: Object(external_gc_i18n_["__"])('列表视图'),
    onClick: toggleListView
  })))), Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-header__actions"
  }, Object(external_gc_element_["createElement"])(save_button, null), Object(external_gc_element_["createElement"])(build_module["h" /* PinnedItems */].Slot, {
    scope: "core/edit-widgets"
  }), Object(external_gc_element_["createElement"])(MoreMenu, null))));
}

/* harmony default export */ var header = (Header);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/notices/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */





function Notices() {
  const {
    removeNotice
  } = Object(external_gc_data_["useDispatch"])(external_gc_notices_["store"]);
  const {
    notices
  } = Object(external_gc_data_["useSelect"])(select => {
    return {
      notices: select(external_gc_notices_["store"]).getNotices()
    };
  }, []);
  const dismissibleNotices = Object(external_lodash_["filter"])(notices, {
    isDismissible: true,
    type: 'default'
  });
  const nonDismissibleNotices = Object(external_lodash_["filter"])(notices, {
    isDismissible: false,
    type: 'default'
  });
  const snackbarNotices = Object(external_lodash_["filter"])(notices, {
    type: 'snackbar'
  });
  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(external_gc_components_["NoticeList"], {
    notices: nonDismissibleNotices,
    className: "edit-widgets-notices__pinned"
  }), Object(external_gc_element_["createElement"])(external_gc_components_["NoticeList"], {
    notices: dismissibleNotices,
    className: "edit-widgets-notices__dismissible",
    onRemove: removeNotice
  }), Object(external_gc_element_["createElement"])(external_gc_components_["SnackbarList"], {
    notices: snackbarNotices,
    className: "edit-widgets-notices__snackbar",
    onRemove: removeNotice
  }));
}

/* harmony default export */ var components_notices = (Notices);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/widget-areas-block-editor-content/index.js


/**
 * GeChiUI dependencies
 */




/**
 * Internal dependencies
 */



function WidgetAreasBlockEditorContent(_ref) {
  let {
    blockEditorSettings
  } = _ref;
  const hasThemeStyles = Object(external_gc_data_["useSelect"])(select => select(build_module["i" /* store */]).isFeatureActive('core/edit-widgets', 'themeStyles'), []);
  const styles = Object(external_gc_element_["useMemo"])(() => {
    return hasThemeStyles ? blockEditorSettings.styles : [];
  }, [blockEditorSettings, hasThemeStyles]);
  return Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-block-editor"
  }, Object(external_gc_element_["createElement"])(components_notices, null), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockTools"], null, Object(external_gc_element_["createElement"])(keyboard_shortcuts, null), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["__unstableEditorStyles"], {
    styles: styles
  }), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockSelectionClearer"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["WritingFlow"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["ObserveTyping"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockList"], {
    className: "edit-widgets-main-block-list"
  }))))));
}

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/close.js
var library_close = __webpack_require__("ajyK");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/hooks/use-widget-library-insertion-point.js
/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */




const useWidgetLibraryInsertionPoint = () => {
  const firstRootId = Object(external_gc_data_["useSelect"])(select => {
    var _widgetAreasPost$bloc;

    // Default to the first widget area
    const {
      getEntityRecord
    } = select(external_gc_coreData_["store"]);
    const widgetAreasPost = getEntityRecord(KIND, POST_TYPE, buildWidgetAreasPostId());
    return widgetAreasPost === null || widgetAreasPost === void 0 ? void 0 : (_widgetAreasPost$bloc = widgetAreasPost.blocks[0]) === null || _widgetAreasPost$bloc === void 0 ? void 0 : _widgetAreasPost$bloc.clientId;
  }, []);
  return Object(external_gc_data_["useSelect"])(select => {
    const {
      getBlockRootClientId,
      getBlockSelectionEnd,
      getBlockOrder,
      getBlockIndex
    } = select(external_gc_blockEditor_["store"]);

    const insertionPoint = select(store).__experimentalGetInsertionPoint(); // "Browse all" in the quick inserter will set the rootClientId to the current block.
    // Otherwise, it will just be undefined, and we'll have to handle it differently below.


    if (insertionPoint.rootClientId) {
      return insertionPoint;
    }

    const clientId = getBlockSelectionEnd() || firstRootId;
    const rootClientId = getBlockRootClientId(clientId); // If the selected block is at the root level, it's a widget area and
    // blocks can't be inserted here. Return this block as the root and the
    // last child clientId indicating insertion at the end.

    if (clientId && rootClientId === '') {
      return {
        rootClientId: clientId,
        insertionIndex: getBlockOrder(clientId).length
      };
    }

    return {
      rootClientId,
      insertionIndex: getBlockIndex(clientId) + 1
    };
  }, [firstRootId]);
};

/* harmony default export */ var use_widget_library_insertion_point = (useWidgetLibraryInsertionPoint);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/secondary-sidebar/inserter-sidebar.js



/**
 * GeChiUI dependencies
 */






/**
 * Internal dependencies
 */



function InserterSidebar() {
  const isMobileViewport = Object(external_gc_compose_["useViewportMatch"])('medium', '<');
  const {
    rootClientId,
    insertionIndex
  } = use_widget_library_insertion_point();
  const {
    setIsInserterOpened
  } = Object(external_gc_data_["useDispatch"])(store);
  const closeInserter = Object(external_gc_element_["useCallback"])(() => {
    return () => setIsInserterOpened(false);
  }, [setIsInserterOpened]);
  const [inserterDialogRef, inserterDialogProps] = Object(external_gc_compose_["__experimentalUseDialog"])({
    onClose: closeInserter
  });
  return Object(external_gc_element_["createElement"])("div", Object(esm_extends["a" /* default */])({
    ref: inserterDialogRef
  }, inserterDialogProps, {
    className: "edit-widgets-layout__inserter-panel"
  }), Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-layout__inserter-panel-header"
  }, Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
    icon: library_close["a" /* default */],
    onClick: closeInserter
  })), Object(external_gc_element_["createElement"])("div", {
    className: "edit-widgets-layout__inserter-panel-content"
  }, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["__experimentalLibrary"], {
    showInserterHelpPanel: true,
    shouldFocusBlock: isMobileViewport,
    rootClientId: rootClientId,
    __experimentalInsertionIndex: insertionIndex
  })));
}

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/close-small.js
var close_small = __webpack_require__("Mg0k");

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/secondary-sidebar/list-view-sidebar.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */


function ListViewSidebar() {
  const {
    setIsListViewOpened
  } = Object(external_gc_data_["useDispatch"])(store);
  const {
    clearSelectedBlock,
    selectBlock
  } = Object(external_gc_data_["useDispatch"])(external_gc_blockEditor_["store"]);

  async function selectEditorBlock(clientId) {
    await clearSelectedBlock();
    selectBlock(clientId, -1);
  }

  const focusOnMountRef = Object(external_gc_compose_["useFocusOnMount"])('firstElement');
  const focusReturnRef = Object(external_gc_compose_["useFocusReturn"])();

  function closeOnEscape(event) {
    if (event.keyCode === external_gc_keycodes_["ESCAPE"] && !event.defaultPrevented) {
      event.preventDefault();
      setIsListViewOpened(false);
    }
  }

  const instanceId = Object(external_gc_compose_["useInstanceId"])(ListViewSidebar);
  const labelId = `edit-widgets-editor__list-view-panel-label-${instanceId}`;
  return (// eslint-disable-next-line jsx-a11y/no-static-element-interactions
    Object(external_gc_element_["createElement"])("div", {
      "aria-labelledby": labelId,
      className: "edit-widgets-editor__list-view-panel",
      onKeyDown: closeOnEscape
    }, Object(external_gc_element_["createElement"])("div", {
      className: "edit-widgets-editor__list-view-panel-header"
    }, Object(external_gc_element_["createElement"])("strong", {
      id: labelId
    }, Object(external_gc_i18n_["__"])('列表视图')), Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
      icon: close_small["a" /* default */],
      label: Object(external_gc_i18n_["__"])('关闭列表试图边栏'),
      onClick: () => setIsListViewOpened(false)
    })), Object(external_gc_element_["createElement"])("div", {
      className: "edit-widgets-editor__list-view-panel-content",
      ref: Object(external_gc_compose_["useMergeRefs"])([focusReturnRef, focusOnMountRef])
    }, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["__experimentalListView"], {
      onSelect: selectEditorBlock,
      showNestedBlocks: true,
      __experimentalHideContainerBlockActions: true,
      __experimentalFeatures: true,
      __experimentalPersistentListViewFeatures: true
    })))
  );
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/secondary-sidebar/index.js


/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Internal dependencies
 */



function SecondarySidebar() {
  const {
    isInserterOpen,
    isListViewOpen
  } = Object(external_gc_data_["useSelect"])(select => {
    const {
      isInserterOpened,
      isListViewOpened
    } = select(store);
    return {
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened()
    };
  }, []);

  if (isInserterOpen) {
    return Object(external_gc_element_["createElement"])(InserterSidebar, null);
  }

  if (isListViewOpen) {
    return Object(external_gc_element_["createElement"])(ListViewSidebar, null);
  }

  return null;
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/layout/interface.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */





const interfaceLabels = {
  /* translators: accessibility text for the widgets screen top bar landmark region. */
  header: Object(external_gc_i18n_["__"])('小工具顶部栏'),

  /* translators: accessibility text for the widgets screen content landmark region. */
  body: Object(external_gc_i18n_["__"])('小工具与区块'),

  /* translators: accessibility text for the widgets screen settings landmark region. */
  sidebar: Object(external_gc_i18n_["__"])('小工具设置'),

  /* translators: accessibility text for the widgets screen footer landmark region. */
  footer: Object(external_gc_i18n_["__"])('小工具页脚')
};

function Interface(_ref) {
  let {
    blockEditorSettings
  } = _ref;
  const isMobileViewport = Object(external_gc_compose_["useViewportMatch"])('medium', '<');
  const isHugeViewport = Object(external_gc_compose_["useViewportMatch"])('huge', '>=');
  const {
    setIsInserterOpened,
    setIsListViewOpened,
    closeGeneralSidebar
  } = Object(external_gc_data_["useDispatch"])(store);
  const {
    hasBlockBreadCrumbsEnabled,
    hasSidebarEnabled,
    isInserterOpened,
    isListViewOpened,
    previousShortcut,
    nextShortcut
  } = Object(external_gc_data_["useSelect"])(select => ({
    hasSidebarEnabled: !!select(build_module["i" /* store */]).getActiveComplementaryArea(store.name),
    isInserterOpened: !!select(store).isInserterOpened(),
    isListViewOpened: !!select(store).isListViewOpened(),
    hasBlockBreadCrumbsEnabled: select(build_module["i" /* store */]).isFeatureActive('core/edit-widgets', 'showBlockBreadcrumbs'),
    previousShortcut: select(external_gc_keyboardShortcuts_["store"]).getAllShortcutKeyCombinations('core/edit-widgets/previous-region'),
    nextShortcut: select(external_gc_keyboardShortcuts_["store"]).getAllShortcutKeyCombinations('core/edit-widgets/next-region')
  }), []); // Inserter and Sidebars are mutually exclusive

  Object(external_gc_element_["useEffect"])(() => {
    if (hasSidebarEnabled && !isHugeViewport) {
      setIsInserterOpened(false);
      setIsListViewOpened(false);
    }
  }, [hasSidebarEnabled, isHugeViewport]);
  Object(external_gc_element_["useEffect"])(() => {
    if ((isInserterOpened || isListViewOpened) && !isHugeViewport) {
      closeGeneralSidebar();
    }
  }, [isInserterOpened, isListViewOpened, isHugeViewport]);
  const hasSecondarySidebar = isListViewOpened || isInserterOpened;
  return Object(external_gc_element_["createElement"])(build_module["e" /* InterfaceSkeleton */], {
    labels: interfaceLabels,
    header: Object(external_gc_element_["createElement"])(header, null),
    secondarySidebar: hasSecondarySidebar && Object(external_gc_element_["createElement"])(SecondarySidebar, null),
    sidebar: hasSidebarEnabled && Object(external_gc_element_["createElement"])(build_module["b" /* ComplementaryArea */].Slot, {
      scope: "core/edit-widgets"
    }),
    content: Object(external_gc_element_["createElement"])(WidgetAreasBlockEditorContent, {
      blockEditorSettings: blockEditorSettings
    }),
    footer: hasBlockBreadCrumbsEnabled && !isMobileViewport && Object(external_gc_element_["createElement"])("div", {
      className: "edit-widgets-layout__footer"
    }, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["BlockBreadcrumb"], {
      rootLabelText: Object(external_gc_i18n_["__"])('小工具')
    })),
    shortcuts: {
      previous: previousShortcut,
      next: nextShortcut
    }
  });
}

/* harmony default export */ var layout_interface = (Interface);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/layout/unsaved-changes-warning.js
/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Warns the user if there are unsaved changes before leaving the editor.
 *
 * This is a duplicate of the component implemented in the editor package.
 * Duplicated here as edit-widgets doesn't depend on editor.
 *
 * @return {GCComponent} The component.
 */

function UnsavedChangesWarning() {
  const isDirty = Object(external_gc_data_["useSelect"])(select => {
    const {
      getEditedWidgetAreas
    } = select(store);
    const editedWidgetAreas = getEditedWidgetAreas();
    return (editedWidgetAreas === null || editedWidgetAreas === void 0 ? void 0 : editedWidgetAreas.length) > 0;
  }, []);
  Object(external_gc_element_["useEffect"])(() => {
    /**
     * Warns the user if there are unsaved changes before leaving the editor.
     *
     * @param {Event} event `beforeunload` event.
     *
     * @return {?string} Warning prompt message, if unsaved changes exist.
     */
    const warnIfUnsavedChanges = event => {
      if (isDirty) {
        event.returnValue = Object(external_gc_i18n_["__"])('您有未保存的修改。如果您继续，这些修改将会丢失。');
        return event.returnValue;
      }
    };

    window.addEventListener('beforeunload', warnIfUnsavedChanges);
    return () => {
      window.removeEventListener('beforeunload', warnIfUnsavedChanges);
    };
  }, [isDirty]);
  return null;
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/welcome-guide/index.js


/**
 * GeChiUI dependencies
 */





/**
 * Internal dependencies
 */


function WelcomeGuide() {
  var _widgetAreas$filter$l;

  const isActive = Object(external_gc_data_["useSelect"])(select => select(build_module["i" /* store */]).isFeatureActive('core/edit-widgets', 'welcomeGuide'), []);
  const {
    toggleFeature
  } = Object(external_gc_data_["useDispatch"])(build_module["i" /* store */]);
  const widgetAreas = Object(external_gc_data_["useSelect"])(select => select(store).getWidgetAreas({
    per_page: -1
  }), []);

  if (!isActive) {
    return null;
  }

  const isEntirelyBlockWidgets = widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.every(widgetArea => widgetArea.id === 'gc_inactive_widgets' || widgetArea.widgets.every(widgetId => widgetId.startsWith('block-')));
  const numWidgetAreas = (_widgetAreas$filter$l = widgetAreas === null || widgetAreas === void 0 ? void 0 : widgetAreas.filter(widgetArea => widgetArea.id !== 'gc_inactive_widgets').length) !== null && _widgetAreas$filter$l !== void 0 ? _widgetAreas$filter$l : 0;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Guide"], {
    className: "edit-widgets-welcome-guide",
    contentLabel: Object(external_gc_i18n_["__"])('欢迎来到区块小工具'),
    finishButtonText: Object(external_gc_i18n_["__"])('从这里开始'),
    onFinish: () => toggleFeature('core/edit-widgets', 'welcomeGuide'),
    pages: [{
      image: Object(external_gc_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
      }),
      content: Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, Object(external_gc_i18n_["__"])('欢迎来到区块小工具')), isEntirelyBlockWidgets ? Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("p", {
        className: "edit-widgets-welcome-guide__text"
      }, Object(external_gc_i18n_["sprintf"])( // Translators: %s: Number of block areas in the current theme.
      Object(external_gc_i18n_["_n"])('', '您的主题提供了不同的"区块"区域供添加和编辑内容。请试着在这里添加搜索栏、社交图标或其他类型的区块并观察其在站点上的显示效果。', numWidgetAreas), numWidgetAreas))) : Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("p", {
        className: "edit-widgets-welcome-guide__text"
      }, Object(external_gc_i18n_["__"])('您现可在站点的小工具区添加任意区块。不用担心，您之前添加的所有小工具仍然可完美运作。')), Object(external_gc_element_["createElement"])("p", {
        className: "edit-widgets-welcome-guide__text"
      }, Object(external_gc_element_["createElement"])("strong", null, Object(external_gc_i18n_["__"])('想坚持使用旧版小工具吗？')), ' ', Object(external_gc_element_["createElement"])(external_gc_components_["ExternalLink"], {
        href: Object(external_gc_i18n_["__"])('https://www.gechiui.com/plugins/classic-widgets/')
      }, Object(external_gc_i18n_["__"])('获取经典小工具插件。')))))
    }, {
      image: Object(external_gc_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
      }),
      content: Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, Object(external_gc_i18n_["__"])('让每个区块彰显其独有的特色')), Object(external_gc_element_["createElement"])("p", {
        className: "edit-widgets-welcome-guide__text"
      }, Object(external_gc_i18n_["__"])('每个区块都可修改颜色、宽度及对齐等属性。这些控件将在您选择区块时自动显示及隐藏。')))
    }, {
      image: Object(external_gc_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
      }),
      content: Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, Object(external_gc_i18n_["__"])('了解区块库')), Object(external_gc_element_["createElement"])("p", {
        className: "edit-widgets-welcome-guide__text"
      }, Object(external_gc_element_["createInterpolateElement"])(Object(external_gc_i18n_["__"])('所有可用的区块都在区块库中。只要看到<InserterIconImage />图标便可找到区块库。'), {
        InserterIconImage: Object(external_gc_element_["createElement"])("img", {
          className: "edit-widgets-welcome-guide__inserter-icon",
          alt: Object(external_gc_i18n_["__"])('插入'),
          src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
        })
      })))
    }, {
      image: Object(external_gc_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
      }),
      content: Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])("h1", {
        className: "edit-widgets-welcome-guide__heading"
      }, Object(external_gc_i18n_["__"])('了解如何使用区块编辑器')), Object(external_gc_element_["createElement"])("p", {
        className: "edit-widgets-welcome-guide__text"
      }, Object(external_gc_i18n_["__"])('刚开始使用区块编辑器？想进一步了解区块编辑器的使用方式？'), Object(external_gc_element_["createElement"])(external_gc_components_["ExternalLink"], {
        href: Object(external_gc_i18n_["__"])('https://www.gechiui.com/support/article/gechiui-editor/')
      }, Object(external_gc_i18n_["__"])("这是详细的指南。"))))
    }]
  });
}

function WelcomeGuideImage(_ref) {
  let {
    nonAnimatedSrc,
    animatedSrc
  } = _ref;
  return Object(external_gc_element_["createElement"])("picture", {
    className: "edit-widgets-welcome-guide__image"
  }, Object(external_gc_element_["createElement"])("source", {
    srcSet: nonAnimatedSrc,
    media: "(prefers-reduced-motion: reduce)"
  }), Object(external_gc_element_["createElement"])("img", {
    src: animatedSrc,
    width: "312",
    height: "240",
    alt: ""
  }));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/components/layout/index.js


/**
 * GeChiUI dependencies
 */


/**
 * Internal dependencies
 */








function Layout(_ref) {
  let {
    blockEditorSettings,
    onError
  } = _ref;
  return Object(external_gc_element_["createElement"])(error_boundary_ErrorBoundary, {
    onError: onError
  }, Object(external_gc_element_["createElement"])(WidgetAreasBlockEditorProvider, {
    blockEditorSettings: blockEditorSettings
  }, Object(external_gc_element_["createElement"])(layout_interface, {
    blockEditorSettings: blockEditorSettings
  }), Object(external_gc_element_["createElement"])(Sidebar, null), Object(external_gc_element_["createElement"])(external_gc_components_["Popover"].Slot, null), Object(external_gc_element_["createElement"])(external_gc_plugins_["PluginArea"], null), Object(external_gc_element_["createElement"])(UnsavedChangesWarning, null), Object(external_gc_element_["createElement"])(WelcomeGuide, null)));
}

/* harmony default export */ var layout = (Layout);

// CONCATENATED MODULE: ./node_modules/@gechiui/edit-widgets/build-module/index.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */






const disabledBlocks = ['core/more', 'core/freeform', 'core/template-part', ...(ALLOW_REUSABLE_BLOCKS ? [] : ['core/block'])];
/**
 * Reinitializes the editor after the user chooses to reboot the editor after
 * an unhandled error occurs, replacing previously mounted editor element using
 * an initial state from prior to the crash.
 *
 * @param {Element} target   DOM node in which editor is rendered.
 * @param {?Object} settings Editor settings object.
 */

function reinitializeEditor(target, settings) {
  Object(external_gc_element_["unmountComponentAtNode"])(target);
  const reboot = reinitializeEditor.bind(null, target, settings);
  Object(external_gc_element_["render"])(Object(external_gc_element_["createElement"])(layout, {
    blockEditorSettings: settings,
    onError: reboot
  }), target);
}
/**
 * Initializes the block editor in the widgets screen.
 *
 * @param {string} id       ID of the root element to render the screen in.
 * @param {Object} settings Block editor settings.
 */

function initialize(id, settings) {
  const target = document.getElementById(id);
  const reboot = reinitializeEditor.bind(null, target, settings);

  const coreBlocks = Object(external_gc_blockLibrary_["__experimentalGetCoreBlocks"])().filter(block => {
    return !(disabledBlocks.includes(block.name) || block.name.startsWith('core/post') || block.name.startsWith('core/query') || block.name.startsWith('core/site') || block.name.startsWith('core/navigation'));
  });

  Object(external_gc_data_["dispatch"])(build_module["i" /* store */]).setFeatureDefaults('core/edit-widgets', {
    fixedToolbar: false,
    welcomeGuide: true,
    showBlockBreadcrumbs: true,
    themeStyles: true
  });

  Object(external_gc_data_["dispatch"])(external_gc_blocks_["store"]).__experimentalReapplyBlockTypeFilters();

  Object(external_gc_blockLibrary_["registerCoreBlocks"])(coreBlocks);
  Object(external_gc_widgets_["registerLegacyWidgetBlock"])();

  if (false) {}

  Object(external_gc_widgets_["registerLegacyWidgetVariations"])(settings);
  registerBlock(widget_area_namespaceObject);
  Object(external_gc_widgets_["registerWidgetGroupBlock"])();

  settings.__experimentalFetchLinkSuggestions = (search, searchOptions) => Object(external_gc_coreData_["__experimentalFetchLinkSuggestions"])(search, searchOptions, settings); // As we are unregistering `core/freeform` to avoid the Classic block, we must
  // replace it with something as the default freeform content handler. Failure to
  // do this will result in errors in the default block parser.
  // see: https://github.com/GeChiUI/gutenberg/issues/33097


  Object(external_gc_blocks_["setFreeformContentHandlerName"])('core/html');
  Object(external_gc_element_["render"])(Object(external_gc_element_["createElement"])(layout, {
    blockEditorSettings: settings,
    onError: reboot
  }), target);
}
/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 *
 */

const registerBlock = block => {
  if (!block) {
    return;
  }

  const {
    metadata,
    settings,
    name
  } = block;

  if (metadata) {
    Object(external_gc_blocks_["unstable__bootstrapServerSideBlockDefinitions"])({
      [name]: metadata
    });
  }

  Object(external_gc_blocks_["registerBlockType"])(name, settings);
};


/***/ }),

/***/ "eTuh":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const moreVertical = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (moreVertical);


/***/ }),

/***/ "ewfG":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["element"]; }());

/***/ }),

/***/ "jd0n":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["components"]; }());

/***/ }),

/***/ "kZIl":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["hooks"]; }());

/***/ }),

/***/ "l35S":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["keycodes"]; }());

/***/ }),

/***/ "lqn5":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const starEmpty = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (starEmpty);


/***/ }),

/***/ "mkk6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const undo = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"
}));
/* harmony default export */ __webpack_exports__["a"] = (undo);


/***/ }),

/***/ "n68F":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["blocks"]; }());

/***/ }),

/***/ "nLrk":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["blockEditor"]; }());

/***/ }),

/***/ "rHI3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const external = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (external);


/***/ }),

/***/ "th/Q":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const plus = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ __webpack_exports__["a"] = (plus);


/***/ }),

/***/ "u+Ow":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["blockLibrary"]; }());

/***/ }),

/***/ "v/OI":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const cog = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fillRule: "evenodd",
  d: "M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (cog);


/***/ }),

/***/ "wx14":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _extends; });
function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

/***/ }),

/***/ "xuem":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["apiFetch"]; }());

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