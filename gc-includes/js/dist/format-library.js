this["gc"] = this["gc"] || {}; this["gc"]["formatLibrary"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "Y+ZS");
/******/ })
/************************************************************************/
/******/ ({

/***/ "0+Iy":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["htmlEntities"]; }());

/***/ }),

/***/ "FOZ3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const linkOff = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.6 7.3h-.7l1.6-3.5-.9-.4-3.9 8.5H9v1.5h2l-1.3 2.8H8.4c-2 0-3.7-1.7-3.7-3.7s1.7-3.7 3.7-3.7H10V7.3H8.4c-2.9 0-5.2 2.3-5.2 5.2 0 2.9 2.3 5.2 5.2 5.2H9l-1.4 3.2.9.4 5.7-12.5h1.4c2 0 3.7 1.7 3.7 3.7s-1.7 3.7-3.7 3.7H14v1.5h1.6c2.9 0 5.2-2.3 5.2-5.2 0-2.9-2.4-5.2-5.2-5.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (linkOff);


/***/ }),

/***/ "Gz8V":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["primitives"]; }());

/***/ }),

/***/ "IgLd":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["data"]; }());

/***/ }),

/***/ "NQKH":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["a11y"]; }());

/***/ }),

/***/ "UEDd":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const button = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M19 6.5H5c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7c0-1.1-.9-2-2-2zm.5 9c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v7zM8 12.8h8v-1.5H8v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (button);


/***/ }),

/***/ "Y+ZS":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external ["gc","richText"]
var external_gc_richText_ = __webpack_require__("kt2g");

// EXTERNAL MODULE: external ["gc","element"]
var external_gc_element_ = __webpack_require__("ewfG");

// EXTERNAL MODULE: external ["gc","i18n"]
var external_gc_i18n_ = __webpack_require__("z4sU");

// EXTERNAL MODULE: external ["gc","blockEditor"]
var external_gc_blockEditor_ = __webpack_require__("nLrk");

// EXTERNAL MODULE: external ["gc","primitives"]
var external_gc_primitives_ = __webpack_require__("Gz8V");

// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/format-bold.js


/**
 * GeChiUI dependencies
 */

const formatBold = Object(external_gc_element_["createElement"])(external_gc_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_gc_element_["createElement"])(external_gc_primitives_["Path"], {
  d: "M14.7 11.3c1-.6 1.5-1.6 1.5-3 0-2.3-1.3-3.4-4-3.4H7v14h5.8c1.4 0 2.5-.3 3.3-1 .8-.7 1.2-1.7 1.2-2.9.1-1.9-.8-3.1-2.6-3.7zm-5.1-4h2.3c.6 0 1.1.1 1.4.4.3.3.5.7.5 1.2s-.2 1-.5 1.2c-.3.3-.8.4-1.4.4H9.6V7.3zm4.6 9c-.4.3-1 .4-1.7.4H9.6v-3.9h2.9c.7 0 1.3.2 1.7.5.4.3.6.8.6 1.5s-.2 1.2-.6 1.5z"
}));
/* harmony default export */ var format_bold = (formatBold);

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/bold/index.js


/**
 * GeChiUI dependencies
 */




const bold_name = 'core/bold';

const title = Object(external_gc_i18n_["__"])('粗体');

const bold = {
  name: bold_name,
  title,
  tagName: 'strong',
  className: null,

  edit(_ref) {
    let {
      isActive,
      value,
      onChange,
      onFocus
    } = _ref;

    function onToggle() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: bold_name,
        title
      }));
    }

    function onClick() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: bold_name
      }));
      onFocus();
    }

    return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "b",
      onUse: onToggle
    }), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
      name: "bold",
      icon: format_bold,
      title: title,
      onClick: onClick,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "b"
    }), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["__unstableRichTextInputEvent"], {
      inputType: "formatBold",
      onInput: onToggle
    }));
  }

};

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/code.js
var code = __webpack_require__("rl/G");

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/code/index.js


/**
 * GeChiUI dependencies
 */




const code_name = 'core/code';

const code_title = Object(external_gc_i18n_["__"])('内联代码');

const code_code = {
  name: code_name,
  title: code_title,
  tagName: 'code',
  className: null,

  __unstableInputRule(value) {
    const BACKTICK = '`';
    const {
      start,
      text
    } = value;
    const characterBefore = text.slice(start - 1, start); // Quick check the text for the necessary character.

    if (characterBefore !== BACKTICK) {
      return value;
    }

    const textBefore = text.slice(0, start - 1);
    const indexBefore = textBefore.lastIndexOf(BACKTICK);

    if (indexBefore === -1) {
      return value;
    }

    const startIndex = indexBefore;
    const endIndex = start - 2;

    if (startIndex === endIndex) {
      return value;
    }

    value = Object(external_gc_richText_["remove"])(value, startIndex, startIndex + 1);
    value = Object(external_gc_richText_["remove"])(value, endIndex, endIndex + 1);
    value = Object(external_gc_richText_["applyFormat"])(value, {
      type: code_name
    }, startIndex, endIndex);
    return value;
  },

  edit(_ref) {
    let {
      value,
      onChange,
      onFocus,
      isActive
    } = _ref;

    function onClick() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: code_name,
        title: code_title
      }));
      onFocus();
    }

    return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
      icon: code["a" /* default */],
      title: code_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

// EXTERNAL MODULE: external ["gc","components"]
var external_gc_components_ = __webpack_require__("jd0n");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/keyboard-return.js
var keyboard_return = __webpack_require__("lyGs");

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/image/index.js


/**
 * GeChiUI dependencies
 */






const ALLOWED_MEDIA_TYPES = ['image'];
const image_name = 'core/image';

const image_title = Object(external_gc_i18n_["__"])('内联图片');

const image_image = {
  name: image_name,
  title: image_title,
  keywords: [Object(external_gc_i18n_["__"])('照片'), Object(external_gc_i18n_["__"])('媒体')],
  object: true,
  tagName: 'img',
  className: null,
  attributes: {
    className: 'class',
    style: 'style',
    url: 'src',
    alt: 'alt'
  },
  edit: Edit
};

function InlineUI(_ref) {
  let {
    value,
    onChange,
    activeObjectAttributes,
    contentRef
  } = _ref;
  const {
    style
  } = activeObjectAttributes;
  const [width, setWidth] = Object(external_gc_element_["useState"])(style === null || style === void 0 ? void 0 : style.replace(/\D/g, ''));
  const anchorRef = Object(external_gc_richText_["useAnchorRef"])({
    ref: contentRef,
    value,
    settings: image_image
  });
  return Object(external_gc_element_["createElement"])(external_gc_components_["Popover"], {
    position: "bottom center",
    focusOnMount: false,
    anchorRef: anchorRef,
    className: "block-editor-format-toolbar__image-popover"
  }, Object(external_gc_element_["createElement"])("form", {
    className: "block-editor-format-toolbar__image-container-content",
    onSubmit: event => {
      const newReplacements = value.replacements.slice();
      newReplacements[value.start] = {
        type: image_name,
        attributes: { ...activeObjectAttributes,
          style: width ? `width: ${width}px;` : ''
        }
      };
      onChange({ ...value,
        replacements: newReplacements
      });
      event.preventDefault();
    }
  }, Object(external_gc_element_["createElement"])(external_gc_components_["TextControl"], {
    className: "block-editor-format-toolbar__image-container-value",
    type: "number",
    label: Object(external_gc_i18n_["__"])('宽度'),
    value: width,
    min: 1,
    onChange: newWidth => setWidth(newWidth)
  }), Object(external_gc_element_["createElement"])(external_gc_components_["Button"], {
    icon: keyboard_return["a" /* default */],
    label: Object(external_gc_i18n_["__"])('应用'),
    type: "submit"
  })));
}

function Edit(_ref2) {
  let {
    value,
    onChange,
    onFocus,
    isObjectActive,
    activeObjectAttributes,
    contentRef
  } = _ref2;
  const [isModalOpen, setIsModalOpen] = Object(external_gc_element_["useState"])(false);

  function openModal() {
    setIsModalOpen(true);
  }

  function closeModal() {
    setIsModalOpen(false);
  }

  return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["MediaUploadCheck"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
    icon: Object(external_gc_element_["createElement"])(external_gc_components_["SVG"], {
      xmlns: "http://www.w3.org/2000/svg",
      viewBox: "0 0 24 24"
    }, Object(external_gc_element_["createElement"])(external_gc_components_["Path"], {
      d: "M4 18.5h16V17H4v1.5zM16 13v1.5h4V13h-4zM5.1 15h7.8c.6 0 1.1-.5 1.1-1.1V6.1c0-.6-.5-1.1-1.1-1.1H5.1C4.5 5 4 5.5 4 6.1v7.8c0 .6.5 1.1 1.1 1.1zm.4-8.5h7V10l-1-1c-.3-.3-.8-.3-1 0l-1.6 1.5-1.2-.7c-.3-.2-.6-.2-.9 0l-1.3 1V6.5zm0 6.1l1.8-1.3 1.3.8c.3.2.7.2.9-.1l1.5-1.4 1.5 1.4v1.5h-7v-.9z"
    })),
    title: image_title,
    onClick: openModal,
    isActive: isObjectActive
  }), isModalOpen && Object(external_gc_element_["createElement"])(external_gc_blockEditor_["MediaUpload"], {
    allowedTypes: ALLOWED_MEDIA_TYPES,
    onSelect: _ref3 => {
      let {
        id,
        url,
        alt,
        width: imgWidth
      } = _ref3;
      closeModal();
      onChange(Object(external_gc_richText_["insertObject"])(value, {
        type: image_name,
        attributes: {
          className: `gc-image-${id}`,
          style: `width: ${Math.min(imgWidth, 150)}px;`,
          url,
          alt
        }
      }));
      onFocus();
    },
    onClose: closeModal,
    render: _ref4 => {
      let {
        open
      } = _ref4;
      open();
      return null;
    }
  }), isObjectActive && Object(external_gc_element_["createElement"])(InlineUI, {
    value: value,
    onChange: onChange,
    activeObjectAttributes: activeObjectAttributes,
    contentRef: contentRef
  }));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/format-italic.js


/**
 * GeChiUI dependencies
 */

const formatItalic = Object(external_gc_element_["createElement"])(external_gc_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_gc_element_["createElement"])(external_gc_primitives_["Path"], {
  d: "M12.5 5L10 19h1.9l2.5-14z"
}));
/* harmony default export */ var format_italic = (formatItalic);

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/italic/index.js


/**
 * GeChiUI dependencies
 */




const italic_name = 'core/italic';

const italic_title = Object(external_gc_i18n_["__"])('斜体');

const italic = {
  name: italic_name,
  title: italic_title,
  tagName: 'em',
  className: null,

  edit(_ref) {
    let {
      isActive,
      value,
      onChange,
      onFocus
    } = _ref;

    function onToggle() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: italic_name,
        title: italic_title
      }));
    }

    function onClick() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: italic_name
      }));
      onFocus();
    }

    return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "i",
      onUse: onToggle
    }), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
      name: "italic",
      icon: format_italic,
      title: italic_title,
      onClick: onClick,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "i"
    }), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["__unstableRichTextInputEvent"], {
      inputType: "formatItalic",
      onInput: onToggle
    }));
  }

};

// EXTERNAL MODULE: external ["gc","url"]
var external_gc_url_ = __webpack_require__("zP/e");

// EXTERNAL MODULE: external ["gc","htmlEntities"]
var external_gc_htmlEntities_ = __webpack_require__("0+Iy");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/link-off.js
var link_off = __webpack_require__("FOZ3");

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/link.js
var library_link = __webpack_require__("qsmw");

// EXTERNAL MODULE: external ["gc","a11y"]
var external_gc_a11y_ = __webpack_require__("NQKH");

// EXTERNAL MODULE: external ["gc","data"]
var external_gc_data_ = __webpack_require__("IgLd");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/link/utils.js
/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */


/**
 * Check for issues with the provided href.
 *
 * @param {string} href The href.
 *
 * @return {boolean} Is the href invalid?
 */

function isValidHref(href) {
  if (!href) {
    return false;
  }

  const trimmedHref = href.trim();

  if (!trimmedHref) {
    return false;
  } // Does the href start with something that looks like a URL protocol?


  if (/^\S+:/.test(trimmedHref)) {
    const protocol = Object(external_gc_url_["getProtocol"])(trimmedHref);

    if (!Object(external_gc_url_["isValidProtocol"])(protocol)) {
      return false;
    } // Add some extra checks for http(s) URIs, since these are the most common use-case.
    // This ensures URIs with an http protocol have exactly two forward slashes following the protocol.


    if (Object(external_lodash_["startsWith"])(protocol, 'http') && !/^https?:\/\/[^\/\s]/i.test(trimmedHref)) {
      return false;
    }

    const authority = Object(external_gc_url_["getAuthority"])(trimmedHref);

    if (!Object(external_gc_url_["isValidAuthority"])(authority)) {
      return false;
    }

    const path = Object(external_gc_url_["getPath"])(trimmedHref);

    if (path && !Object(external_gc_url_["isValidPath"])(path)) {
      return false;
    }

    const queryString = Object(external_gc_url_["getQueryString"])(trimmedHref);

    if (queryString && !Object(external_gc_url_["isValidQueryString"])(queryString)) {
      return false;
    }

    const fragment = Object(external_gc_url_["getFragment"])(trimmedHref);

    if (fragment && !Object(external_gc_url_["isValidFragment"])(fragment)) {
      return false;
    }
  } // Validate anchor links.


  if (Object(external_lodash_["startsWith"])(trimmedHref, '#') && !Object(external_gc_url_["isValidFragment"])(trimmedHref)) {
    return false;
  }

  return true;
}
/**
 * Generates the format object that will be applied to the link text.
 *
 * @param {Object}  options
 * @param {string}  options.url              The href of the link.
 * @param {string}  options.type             The type of the link.
 * @param {string}  options.id               The ID of the link.
 * @param {boolean} options.opensInNewWindow Whether this link will open in a new window.
 *
 * @return {Object} The final format object.
 */

function createLinkFormat(_ref) {
  let {
    url,
    type,
    id,
    opensInNewWindow
  } = _ref;
  const format = {
    type: 'core/link',
    attributes: {
      url
    }
  };
  if (type) format.attributes.type = type;
  if (id) format.attributes.id = id;

  if (opensInNewWindow) {
    format.attributes.target = '_blank';
    format.attributes.rel = 'noreferrer noopener';
  }

  return format;
}
/* eslint-disable jsdoc/no-undefined-types */

/**
 * Get the start and end boundaries of a given format from a rich text value.
 *
 *
 * @param {RichTextValue} value      the rich text value to interrogate.
 * @param {string}        format     the identifier for the target format (e.g. `core/link`, `core/bold`).
 * @param {number?}       startIndex optional startIndex to seek from.
 * @param {number?}       endIndex   optional endIndex to seek from.
 * @return {Object}	object containing start and end values for the given format.
 */

/* eslint-enable jsdoc/no-undefined-types */

function getFormatBoundary(value, format) {
  let startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.start;
  let endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : value.end;
  const EMPTY_BOUNDARIES = {
    start: null,
    end: null
  };
  const {
    formats
  } = value;
  let targetFormat;
  let initialIndex;

  if (!(formats !== null && formats !== void 0 && formats.length)) {
    return EMPTY_BOUNDARIES;
  } // Clone formats to avoid modifying source formats.


  const newFormats = formats.slice();
  const formatAtStart = Object(external_lodash_["find"])(newFormats[startIndex], {
    type: format.type
  });
  const formatAtEnd = Object(external_lodash_["find"])(newFormats[endIndex], {
    type: format.type
  });
  const formatAtEndMinusOne = Object(external_lodash_["find"])(newFormats[endIndex - 1], {
    type: format.type
  });

  if (!!formatAtStart) {
    // Set values to conform to "start"
    targetFormat = formatAtStart;
    initialIndex = startIndex;
  } else if (!!formatAtEnd) {
    // Set values to conform to "end"
    targetFormat = formatAtEnd;
    initialIndex = endIndex;
  } else if (!!formatAtEndMinusOne) {
    // This is an edge case which will occur if you create a format, then place
    // the caret just before the format and hit the back ARROW key. The resulting
    // value object will have start and end +1 beyond the edge of the format boundary.
    targetFormat = formatAtEndMinusOne;
    initialIndex = endIndex - 1;
  } else {
    return EMPTY_BOUNDARIES;
  }

  const index = newFormats[initialIndex].indexOf(targetFormat);
  const walkingArgs = [newFormats, initialIndex, targetFormat, index]; // Walk the startIndex "backwards" to the leading "edge" of the matching format.

  startIndex = walkToStart(...walkingArgs); // Walk the endIndex "forwards" until the trailing "edge" of the matching format.

  endIndex = walkToEnd(...walkingArgs); // Safe guard: start index cannot be less than 0

  startIndex = startIndex < 0 ? 0 : startIndex; // // Return the indicies of the "edges" as the boundaries.

  return {
    start: startIndex,
    end: endIndex
  };
}
/**
 * Walks forwards/backwards towards the boundary of a given format within an
 * array of format objects. Returns the index of the boundary.
 *
 * @param {Array}  formats         the formats to search for the given format type.
 * @param {number} initialIndex    the starting index from which to walk.
 * @param {Object} targetFormatRef a reference to the format type object being sought.
 * @param {number} formatIndex     the index at which we expect the target format object to be.
 * @param {string} direction       either 'forwards' or 'backwards' to indicate the direction.
 * @return {number} the index of the boundary of the given format.
 */

function walkToBoundary(formats, initialIndex, targetFormatRef, formatIndex, direction) {
  let index = initialIndex;
  const directions = {
    forwards: 1,
    backwards: -1
  };
  const directionIncrement = directions[direction] || 1; // invalid direction arg default to forwards

  const inverseDirectionIncrement = directionIncrement * -1;

  while (formats[index] && formats[index][formatIndex] === targetFormatRef) {
    // Increment/decrement in the direction of operation.
    index = index + directionIncrement;
  } // Restore by one in inverse direction of operation
  // to avoid out of bounds.


  index = index + inverseDirectionIncrement;
  return index;
}

const walkToStart = Object(external_lodash_["partialRight"])(walkToBoundary, 'backwards');
const walkToEnd = Object(external_lodash_["partialRight"])(walkToBoundary, 'forwards');

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/link/use-link-instance-key.js
// Weakly referenced map allows unused ids to be garbage collected.
const weakMap = new WeakMap(); // Incrementing zero-based ID value

let use_link_instance_key_id = -1;
const prefix = 'link-control-instance';

function getKey(_id) {
  return `${prefix}-${_id}`;
}
/**
 * Builds a unique link control key for the given object reference.
 *
 * @param {Object} instance an unique object reference specific to this link control instance.
 * @return {string} the unique key to use for this link control.
 */


function useLinkInstanceKey(instance) {
  if (!instance) {
    return;
  }

  if (weakMap.has(instance)) {
    return getKey(weakMap.get(instance));
  }

  use_link_instance_key_id += 1;
  weakMap.set(instance, use_link_instance_key_id);
  return getKey(use_link_instance_key_id);
}

/* harmony default export */ var use_link_instance_key = (useLinkInstanceKey);

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/link/inline.js


/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */





function InlineLinkUI(_ref) {
  let {
    isActive,
    activeAttributes,
    addingLink,
    value,
    onChange,
    speak,
    stopAddingLink,
    contentRef
  } = _ref;
  const richLinkTextValue = getRichTextValueFromSelection(value, isActive); // Get the text content minus any HTML tags.

  const richTextText = richLinkTextValue.text;
  /**
   * Pending settings to be applied to the next link. When inserting a new
   * link, toggle values cannot be applied immediately, because there is not
   * yet a link for them to apply to. Thus, they are maintained in a state
   * value until the time that the link can be inserted or edited.
   *
   * @type {[Object|undefined,Function]}
   */

  const [nextLinkValue, setNextLinkValue] = Object(external_gc_element_["useState"])();
  const {
    createPageEntity,
    userCanCreatePages
  } = Object(external_gc_data_["useSelect"])(select => {
    const {
      getSettings
    } = select(external_gc_blockEditor_["store"]);

    const _settings = getSettings();

    return {
      createPageEntity: _settings.__experimentalCreatePageEntity,
      userCanCreatePages: _settings.__experimentalUserCanCreatePages
    };
  }, []);
  const linkValue = {
    url: activeAttributes.url,
    type: activeAttributes.type,
    id: activeAttributes.id,
    opensInNewTab: activeAttributes.target === '_blank',
    title: richTextText,
    ...nextLinkValue
  };

  function removeLink() {
    const newValue = Object(external_gc_richText_["removeFormat"])(value, 'core/link');
    onChange(newValue);
    stopAddingLink();
    speak(Object(external_gc_i18n_["__"])('已移除链接。'), 'assertive');
  }

  function onChangeLink(nextValue) {
    // Merge with values from state, both for the purpose of assigning the
    // next state value, and for use in constructing the new link format if
    // the link is ready to be applied.
    nextValue = { ...nextLinkValue,
      ...nextValue
    }; // LinkControl calls `onChange` immediately upon the toggling a setting.

    const didToggleSetting = linkValue.opensInNewTab !== nextValue.opensInNewTab && linkValue.url === nextValue.url; // If change handler was called as a result of a settings change during
    // link insertion, it must be held in state until the link is ready to
    // be applied.

    const didToggleSettingForNewLink = didToggleSetting && nextValue.url === undefined; // If link will be assigned, the state value can be considered flushed.
    // Otherwise, persist the pending changes.

    setNextLinkValue(didToggleSettingForNewLink ? nextValue : undefined);

    if (didToggleSettingForNewLink) {
      return;
    }

    const newUrl = Object(external_gc_url_["prependHTTP"])(nextValue.url);
    const linkFormat = createLinkFormat({
      url: newUrl,
      type: nextValue.type,
      id: nextValue.id !== undefined && nextValue.id !== null ? String(nextValue.id) : undefined,
      opensInNewWindow: nextValue.opensInNewTab
    });
    const newText = nextValue.title || newUrl;

    if (Object(external_gc_richText_["isCollapsed"])(value) && !isActive) {
      // Scenario: we don't have any actively selected text or formats.
      const toInsert = Object(external_gc_richText_["applyFormat"])(Object(external_gc_richText_["create"])({
        text: newText
      }), linkFormat, 0, newText.length);
      onChange(Object(external_gc_richText_["insert"])(value, toInsert));
    } else {
      // Scenario: we have any active text selection or an active format
      let newValue;

      if (newText === richTextText) {
        // If we're not updating the text then ignore
        newValue = Object(external_gc_richText_["applyFormat"])(value, linkFormat);
      } else {
        // Create new RichText value for the new text in order that we
        // can apply formats to it.
        newValue = Object(external_gc_richText_["create"])({
          text: newText
        }); // Apply the new Link format to this new text value.

        newValue = Object(external_gc_richText_["applyFormat"])(newValue, linkFormat, 0, newText.length); // Update the original (full) RichTextValue replacing the
        // target text with the *new* RichTextValue containing:
        // 1. The new text content.
        // 2. The new link format.
        // Note original formats will be lost when applying this change.
        // That is expected behaviour.
        // See: https://github.com/GeChiUI/gutenberg/pull/33849#issuecomment-936134179.

        newValue = Object(external_gc_richText_["replace"])(value, richTextText, newValue);
      }

      newValue.start = newValue.end;
      newValue.activeFormats = [];
      onChange(newValue);
    } // Focus should only be shifted back to the formatted segment when the
    // URL is submitted.


    if (!didToggleSetting) {
      stopAddingLink();
    }

    if (!isValidHref(newUrl)) {
      speak(Object(external_gc_i18n_["__"])('警告：此链接已被插入但可能含有错误，请测试。'), 'assertive');
    } else if (isActive) {
      speak(Object(external_gc_i18n_["__"])('已编辑链接。'), 'assertive');
    } else {
      speak(Object(external_gc_i18n_["__"])('链接已插入。'), 'assertive');
    }
  }

  const anchorRef = Object(external_gc_richText_["useAnchorRef"])({
    ref: contentRef,
    value,
    settings: link_link
  }); // Generate a string based key that is unique to this anchor reference.
  // This is used to force re-mount the LinkControl component to avoid
  // potential stale state bugs caused by the component not being remounted
  // See https://github.com/GeChiUI/gutenberg/pull/34742.

  const forceRemountKey = use_link_instance_key(anchorRef); // The focusOnMount prop shouldn't evolve during render of a Popover
  // otherwise it causes a render of the content.

  const focusOnMount = Object(external_gc_element_["useRef"])(addingLink ? 'firstElement' : false);

  async function handleCreate(pageTitle) {
    const page = await createPageEntity({
      title: pageTitle,
      status: 'draft'
    });
    return {
      id: page.id,
      type: page.type,
      title: page.title.rendered,
      url: page.link,
      kind: 'post-type'
    };
  }

  function createButtonText(searchTerm) {
    return Object(external_gc_element_["createInterpolateElement"])(Object(external_gc_i18n_["sprintf"])(
    /* translators: %s: search term. */
    Object(external_gc_i18n_["__"])('创建页面：<mark>%s</mark>'), searchTerm), {
      mark: Object(external_gc_element_["createElement"])("mark", null)
    });
  }

  return Object(external_gc_element_["createElement"])(external_gc_components_["Popover"], {
    anchorRef: anchorRef,
    focusOnMount: focusOnMount.current,
    onClose: stopAddingLink,
    position: "bottom center"
  }, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["__experimentalLinkControl"], {
    key: forceRemountKey,
    value: linkValue,
    onChange: onChangeLink,
    onRemove: removeLink,
    forceIsEditingLink: addingLink,
    hasRichPreviews: true,
    createSuggestion: createPageEntity && handleCreate,
    withCreateSuggestion: userCanCreatePages,
    createSuggestionButtonText: createButtonText,
    hasTextControl: true
  }));
}

function getRichTextValueFromSelection(value, isActive) {
  // Default to the selection ranges on the RichTextValue object.
  let textStart = value.start;
  let textEnd = value.end; // If the format is currently active then the rich text value
  // should always be taken from the bounds of the active format
  // and not the selected text.

  if (isActive) {
    const boundary = getFormatBoundary(value, {
      type: 'core/link'
    });
    textStart = boundary.start; // Text *selection* always extends +1 beyond the edge of the format.
    // We account for that here.

    textEnd = boundary.end + 1;
  } // Get a RichTextValue containing the selected text content.


  return Object(external_gc_richText_["slice"])(value, textStart, textEnd);
}

/* harmony default export */ var inline = (Object(external_gc_components_["withSpokenMessages"])(InlineLinkUI));

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/link/index.js


/**
 * GeChiUI dependencies
 */








/**
 * Internal dependencies
 */



const link_name = 'core/link';

const link_title = Object(external_gc_i18n_["__"])('链接');

function link_Edit(_ref) {
  let {
    isActive,
    activeAttributes,
    value,
    onChange,
    onFocus,
    contentRef
  } = _ref;
  const [addingLink, setAddingLink] = Object(external_gc_element_["useState"])(false);

  function addLink() {
    const text = Object(external_gc_richText_["getTextContent"])(Object(external_gc_richText_["slice"])(value));

    if (text && Object(external_gc_url_["isURL"])(text) && isValidHref(text)) {
      onChange(Object(external_gc_richText_["applyFormat"])(value, {
        type: link_name,
        attributes: {
          url: text
        }
      }));
    } else if (text && Object(external_gc_url_["isEmail"])(text)) {
      onChange(Object(external_gc_richText_["applyFormat"])(value, {
        type: link_name,
        attributes: {
          url: `mailto:${text}`
        }
      }));
    } else {
      setAddingLink(true);
    }
  }

  function stopAddingLink() {
    setAddingLink(false);
    onFocus();
  }

  function onRemoveFormat() {
    onChange(Object(external_gc_richText_["removeFormat"])(value, link_name));
    Object(external_gc_a11y_["speak"])(Object(external_gc_i18n_["__"])('已移除链接。'), 'assertive');
  }

  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextShortcut"], {
    type: "primary",
    character: "k",
    onUse: addLink
  }), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextShortcut"], {
    type: "primaryShift",
    character: "k",
    onUse: onRemoveFormat
  }), isActive && Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
    name: "link",
    icon: link_off["a" /* default */],
    title: Object(external_gc_i18n_["__"])('移除链接'),
    onClick: onRemoveFormat,
    isActive: isActive,
    shortcutType: "primaryShift",
    shortcutCharacter: "k"
  }), !isActive && Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
    name: "link",
    icon: library_link["a" /* default */],
    title: link_title,
    onClick: addLink,
    isActive: isActive,
    shortcutType: "primary",
    shortcutCharacter: "k"
  }), (addingLink || isActive) && Object(external_gc_element_["createElement"])(inline, {
    addingLink: addingLink,
    stopAddingLink: stopAddingLink,
    isActive: isActive,
    activeAttributes: activeAttributes,
    value: value,
    onChange: onChange,
    contentRef: contentRef
  }));
}

const link_link = {
  name: link_name,
  title: link_title,
  tagName: 'a',
  className: null,
  attributes: {
    url: 'href',
    type: 'data-type',
    id: 'data-id',
    target: 'target'
  },

  __unstablePasteRule(value, _ref2) {
    let {
      html,
      plainText
    } = _ref2;

    if (Object(external_gc_richText_["isCollapsed"])(value)) {
      return value;
    }

    const pastedText = (html || plainText).replace(/<[^>]+>/g, '').trim(); // A URL was pasted, turn the selection into a link

    if (!Object(external_gc_url_["isURL"])(pastedText)) {
      return value;
    } // Allows us to ask for this information when we get a report.


    window.console.log('Created link:\n\n', pastedText);
    return Object(external_gc_richText_["applyFormat"])(value, {
      type: link_name,
      attributes: {
        url: Object(external_gc_htmlEntities_["decodeEntities"])(pastedText)
      }
    });
  },

  edit: link_Edit
};

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/format-strikethrough.js
var format_strikethrough = __webpack_require__("tYCf");

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/strikethrough/index.js


/**
 * GeChiUI dependencies
 */




const strikethrough_name = 'core/strikethrough';

const strikethrough_title = Object(external_gc_i18n_["__"])('删除线');

const strikethrough = {
  name: strikethrough_name,
  title: strikethrough_title,
  tagName: 's',
  className: null,

  edit(_ref) {
    let {
      isActive,
      value,
      onChange,
      onFocus
    } = _ref;

    function onClick() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: strikethrough_name,
        title: strikethrough_title
      }));
      onFocus();
    }

    return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
      icon: format_strikethrough["a" /* default */],
      title: strikethrough_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/underline/index.js


/**
 * GeChiUI dependencies
 */



const underline_name = 'core/underline';

const underline_title = Object(external_gc_i18n_["__"])('下划线');

const underline = {
  name: underline_name,
  title: underline_title,
  tagName: 'span',
  className: null,
  attributes: {
    style: 'style'
  },

  edit(_ref) {
    let {
      value,
      onChange
    } = _ref;

    const onToggle = () => {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: underline_name,
        attributes: {
          style: 'text-decoration: underline;'
        },
        title: underline_title
      }));
    };

    return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextShortcut"], {
      type: "primary",
      character: "u",
      onUse: onToggle
    }), Object(external_gc_element_["createElement"])(external_gc_blockEditor_["__unstableRichTextInputEvent"], {
      inputType: "formatUnderline",
      onInput: onToggle
    }));
  }

};

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/icon/index.js
var icon = __webpack_require__("txjq");

// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/text-color.js


/**
 * GeChiUI dependencies
 */

const textColor = Object(external_gc_element_["createElement"])(external_gc_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_gc_element_["createElement"])(external_gc_primitives_["Path"], {
  d: "M12.9 6h-2l-4 11h1.9l1.1-3h4.2l1.1 3h1.9L12.9 6zm-2.5 6.5l1.5-4.9 1.7 4.9h-3.2z"
}));
/* harmony default export */ var text_color = (textColor);

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/text-color/inline.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */







/**
 * Internal dependencies
 */



function parseCSS() {
  let css = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  return css.split(';').reduce((accumulator, rule) => {
    if (rule) {
      const [property, value] = rule.split(':');
      if (property === 'color') accumulator.color = value;
      if (property === 'background-color' && value !== transparentValue) accumulator.backgroundColor = value;
    }

    return accumulator;
  }, {});
}

function parseClassName() {
  let className = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  let colorSettings = arguments.length > 1 ? arguments[1] : undefined;
  return className.split(' ').reduce((accumulator, name) => {
    // `colorSlug` could contain dashes, so simply match the start and end.
    if (name.startsWith('has-') && name.endsWith('-color')) {
      const colorSlug = name.replace(/^has-/, '').replace(/-color$/, '');
      const colorObject = Object(external_gc_blockEditor_["getColorObjectByAttributeValues"])(colorSettings, colorSlug);
      accumulator.color = colorObject.color;
    }

    return accumulator;
  }, {});
}

function getActiveColors(value, name, colorSettings) {
  const activeColorFormat = Object(external_gc_richText_["getActiveFormat"])(value, name);

  if (!activeColorFormat) {
    return {};
  }

  return { ...parseCSS(activeColorFormat.attributes.style),
    ...parseClassName(activeColorFormat.attributes.class, colorSettings)
  };
}

function setColors(value, name, colorSettings, colors) {
  const {
    color,
    backgroundColor
  } = { ...getActiveColors(value, name, colorSettings),
    ...colors
  };

  if (!color && !backgroundColor) {
    return Object(external_gc_richText_["removeFormat"])(value, name);
  }

  const styles = [];
  const classNames = [];
  const attributes = {};

  if (backgroundColor) {
    styles.push(['background-color', backgroundColor].join(':'));
  } else {
    // Override default browser color for mark element.
    styles.push(['background-color', transparentValue].join(':'));
  }

  if (color) {
    const colorObject = Object(external_gc_blockEditor_["getColorObjectByColorValue"])(colorSettings, color);

    if (colorObject) {
      classNames.push(Object(external_gc_blockEditor_["getColorClassName"])('color', colorObject.slug));
    } else {
      styles.push(['color', color].join(':'));
    }
  }

  if (styles.length) attributes.style = styles.join(';');
  if (classNames.length) attributes.class = classNames.join(' ');
  return Object(external_gc_richText_["applyFormat"])(value, {
    type: name,
    attributes
  });
}

function ColorPicker(_ref) {
  let {
    name,
    property,
    value,
    onChange
  } = _ref;
  const colors = Object(external_gc_data_["useSelect"])(select => {
    const {
      getSettings
    } = select(external_gc_blockEditor_["store"]);
    return Object(external_lodash_["get"])(getSettings(), ['colors'], []);
  }, []);
  const onColorChange = Object(external_gc_element_["useCallback"])(color => {
    onChange(setColors(value, name, colors, {
      [property]: color
    }));
  }, [colors, onChange, property]);
  const activeColors = Object(external_gc_element_["useMemo"])(() => getActiveColors(value, name, colors), [name, value, colors]);
  return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["ColorPalette"], {
    value: activeColors[property],
    onChange: onColorChange
  });
}

function InlineColorUI(_ref2) {
  let {
    name,
    value,
    onChange,
    onClose,
    contentRef
  } = _ref2;

  /* 
   As you change the text color by typing a HEX value into a field,
   the return value of document.getSelection jumps to the field you're editing,
   not the highlighted text. Given that useAnchorRef uses document.getSelection,
   it will return null, since it can't find the <mark> element within the HEX input.
   This caches the last truthy value of the selection anchor reference.
   */
  const anchorRef = Object(external_gc_blockEditor_["useCachedTruthy"])(Object(external_gc_richText_["useAnchorRef"])({
    ref: contentRef,
    value,
    settings: text_color_textColor
  }));
  return Object(external_gc_element_["createElement"])(external_gc_components_["Popover"], {
    onClose: onClose,
    className: "components-inline-color-popover",
    anchorRef: anchorRef
  }, Object(external_gc_element_["createElement"])(external_gc_components_["TabPanel"], {
    tabs: [{
      name: 'color',
      title: Object(external_gc_i18n_["__"])('文字')
    }, {
      name: 'backgroundColor',
      title: Object(external_gc_i18n_["__"])('背景')
    }]
  }, tab => Object(external_gc_element_["createElement"])(ColorPicker, {
    name: name,
    property: tab.name,
    value: value,
    onChange: onChange
  })));
}

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/text-color/index.js


/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */






/**
 * Internal dependencies
 */


const transparentValue = 'rgba(0, 0, 0, 0)';
const text_color_name = 'core/text-color';

const text_color_title = Object(external_gc_i18n_["__"])('高亮');

const EMPTY_ARRAY = [];

function getComputedStyleProperty(element, property) {
  const {
    ownerDocument
  } = element;
  const {
    defaultView
  } = ownerDocument;
  const style = defaultView.getComputedStyle(element);
  const value = style.getPropertyValue(property);

  if (property === 'background-color' && value === transparentValue && element.parentElement) {
    return getComputedStyleProperty(element.parentElement, property);
  }

  return value;
}

function fillComputedColors(element, _ref) {
  let {
    color,
    backgroundColor
  } = _ref;

  if (!color && !backgroundColor) {
    return;
  }

  return {
    color: color || getComputedStyleProperty(element, 'color'),
    backgroundColor: backgroundColor === transparentValue ? getComputedStyleProperty(element, 'background-color') : backgroundColor
  };
}

function TextColorEdit(_ref2) {
  let {
    value,
    onChange,
    isActive,
    activeAttributes,
    contentRef
  } = _ref2;
  const allowCustomControl = Object(external_gc_blockEditor_["useSetting"])('color.custom');
  const colors = Object(external_gc_blockEditor_["useSetting"])('color.palette') || EMPTY_ARRAY;
  const [isAddingColor, setIsAddingColor] = Object(external_gc_element_["useState"])(false);
  const enableIsAddingColor = Object(external_gc_element_["useCallback"])(() => setIsAddingColor(true), [setIsAddingColor]);
  const disableIsAddingColor = Object(external_gc_element_["useCallback"])(() => setIsAddingColor(false), [setIsAddingColor]);
  const colorIndicatorStyle = Object(external_gc_element_["useMemo"])(() => fillComputedColors(contentRef.current, getActiveColors(value, text_color_name, colors)), [value, colors]);
  const hasColorsToChoose = !Object(external_lodash_["isEmpty"])(colors) || !allowCustomControl;

  if (!hasColorsToChoose && !isActive) {
    return null;
  }

  return Object(external_gc_element_["createElement"])(external_gc_element_["Fragment"], null, Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
    className: "format-library-text-color-button",
    isActive: isActive,
    icon: Object(external_gc_element_["createElement"])(icon["a" /* default */], {
      icon: text_color,
      style: colorIndicatorStyle
    }),
    title: text_color_title // If has no colors to choose but a color is active remove the color onClick
    ,
    onClick: hasColorsToChoose ? enableIsAddingColor : () => onChange(Object(external_gc_richText_["removeFormat"])(value, text_color_name)),
    role: "menuitemcheckbox"
  }), isAddingColor && Object(external_gc_element_["createElement"])(InlineColorUI, {
    name: text_color_name,
    onClose: disableIsAddingColor,
    activeAttributes: activeAttributes,
    value: value,
    onChange: onChange,
    contentRef: contentRef
  }));
}

const text_color_textColor = {
  name: text_color_name,
  title: text_color_title,
  tagName: 'mark',
  className: 'has-inline-color',
  attributes: {
    style: 'style',
    class: 'class'
  },

  /*
   * Since this format relies on the <mark> tag, it's important to
   * prevent the default yellow background color applied by most
   * browsers. The solution is to detect when this format is used with a
   * text color but no background color, and in such cases to override
   * the default styling with a transparent background.
   *
   * @see https://github.com/GeChiUI/gutenberg/pull/35516
   */
  __unstableFilterAttributeValue(key, value) {
    if (key !== 'style') return value; // We should not add a background-color if it's already set

    if (value && value.includes('background-color')) return value;
    const addedCSS = ['background-color', transparentValue].join(':'); // Prepend `addedCSS` to avoid a double `;;` as any the existing CSS
    // rules will already include a `;`.

    return value ? [addedCSS, value].join(';') : addedCSS;
  },

  edit: TextColorEdit
};

// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/subscript.js


/**
 * GeChiUI dependencies
 */

const subscript = Object(external_gc_element_["createElement"])(external_gc_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_gc_element_["createElement"])(external_gc_primitives_["Path"], {
  d: "M16.9 18.3l.8-1.2c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.1-.3-.4-.5-.6-.7-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.2 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3L15 19.4h4.3v-1.2h-2.4zM14.1 7.2h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z"
}));
/* harmony default export */ var library_subscript = (subscript);

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/subscript/index.js


/**
 * GeChiUI dependencies
 */




const subscript_name = 'core/subscript';

const subscript_title = Object(external_gc_i18n_["__"])('下标');

const subscript_subscript = {
  name: subscript_name,
  title: subscript_title,
  tagName: 'sub',
  className: null,

  edit(_ref) {
    let {
      isActive,
      value,
      onChange,
      onFocus
    } = _ref;

    function onToggle() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: subscript_name,
        title: subscript_title
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
      icon: library_subscript,
      title: subscript_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

// CONCATENATED MODULE: ./node_modules/@gechiui/icons/build-module/library/superscript.js


/**
 * GeChiUI dependencies
 */

const superscript = Object(external_gc_element_["createElement"])(external_gc_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_gc_element_["createElement"])(external_gc_primitives_["Path"], {
  d: "M16.9 10.3l.8-1.3c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.2-.2-.4-.4-.7-.6-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.1 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3l-1.8 2.8h4.3v-1.2h-2.2zm-2.8-3.1h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z"
}));
/* harmony default export */ var library_superscript = (superscript);

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/superscript/index.js


/**
 * GeChiUI dependencies
 */




const superscript_name = 'core/superscript';

const superscript_title = Object(external_gc_i18n_["__"])('上标');

const superscript_superscript = {
  name: superscript_name,
  title: superscript_title,
  tagName: 'sup',
  className: null,

  edit(_ref) {
    let {
      isActive,
      value,
      onChange,
      onFocus
    } = _ref;

    function onToggle() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: superscript_name,
        title: superscript_title
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
      icon: library_superscript,
      title: superscript_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

// EXTERNAL MODULE: ./node_modules/@gechiui/icons/build-module/library/button.js
var library_button = __webpack_require__("UEDd");

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/keyboard/index.js


/**
 * GeChiUI dependencies
 */




const keyboard_name = 'core/keyboard';

const keyboard_title = Object(external_gc_i18n_["__"])('键盘输入');

const keyboard = {
  name: keyboard_name,
  title: keyboard_title,
  tagName: 'kbd',
  className: null,

  edit(_ref) {
    let {
      isActive,
      value,
      onChange,
      onFocus
    } = _ref;

    function onToggle() {
      onChange(Object(external_gc_richText_["toggleFormat"])(value, {
        type: keyboard_name,
        title: keyboard_title
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return Object(external_gc_element_["createElement"])(external_gc_blockEditor_["RichTextToolbarButton"], {
      icon: library_button["a" /* default */],
      title: keyboard_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/default-formats.js
/**
 * Internal dependencies
 */











/* harmony default export */ var default_formats = ([bold, code_code, image_image, italic, link_link, strikethrough, underline, text_color_textColor, subscript_subscript, superscript_superscript, keyboard]);

// CONCATENATED MODULE: ./node_modules/@gechiui/format-library/build-module/index.js
/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */


default_formats.forEach(_ref => {
  let {
    name,
    ...settings
  } = _ref;
  return Object(external_gc_richText_["registerFormatType"])(name, settings);
});


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

/***/ "kt2g":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["richText"]; }());

/***/ }),

/***/ "lyGs":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const keyboardReturn = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M6.734 16.106l2.176-2.38-1.093-1.028-3.846 4.158 3.846 4.157 1.093-1.027-2.176-2.38h2.811c1.125 0 2.25.03 3.374 0 1.428-.001 3.362-.25 4.963-1.277 1.66-1.065 2.868-2.906 2.868-5.859 0-2.479-1.327-4.896-3.65-5.93-1.82-.813-3.044-.8-4.806-.788l-.567.002v1.5c.184 0 .368 0 .553-.002 1.82-.007 2.704-.014 4.21.657 1.854.827 2.76 2.657 2.76 4.561 0 2.472-.973 3.824-2.178 4.596-1.258.807-2.864 1.04-4.163 1.04h-.02c-1.115.03-2.229 0-3.344 0H6.734z"
}));
/* harmony default export */ __webpack_exports__["a"] = (keyboardReturn);


/***/ }),

/***/ "nLrk":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["blockEditor"]; }());

/***/ }),

/***/ "qsmw":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const link = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.6 7.2H14v1.5h1.6c2 0 3.7 1.7 3.7 3.7s-1.7 3.7-3.7 3.7H14v1.5h1.6c2.8 0 5.2-2.3 5.2-5.2 0-2.9-2.3-5.2-5.2-5.2zM4.7 12.4c0-2 1.7-3.7 3.7-3.7H10V7.2H8.4c-2.9 0-5.2 2.3-5.2 5.2 0 2.9 2.3 5.2 5.2 5.2H10v-1.5H8.4c-2 0-3.7-1.7-3.7-3.7zm4.6.9h5.3v-1.5H9.3v1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (link);


/***/ }),

/***/ "rl/G":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const code = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z"
}));
/* harmony default export */ __webpack_exports__["a"] = (code);


/***/ }),

/***/ "tYCf":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Gz8V");
/* harmony import */ var _gechiui_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * GeChiUI dependencies
 */

const formatStrikethrough = Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_gechiui_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M9.1 9v-.5c0-.6.2-1.1.7-1.4.5-.3 1.2-.5 2-.5.7 0 1.4.1 2.1.3.7.2 1.4.5 2.1.9l.2-1.9c-.6-.3-1.2-.5-1.9-.7-.8-.1-1.6-.2-2.4-.2-1.5 0-2.7.3-3.6 1-.8.7-1.2 1.5-1.2 2.6V9h2zM20 12H4v1h8.3c.3.1.6.2.8.3.5.2.9.5 1.1.8.3.3.4.7.4 1.2 0 .7-.2 1.1-.8 1.5-.5.3-1.2.5-2.1.5-.8 0-1.6-.1-2.4-.3-.8-.2-1.5-.5-2.2-.8L7 18.1c.5.2 1.2.4 2 .6.8.2 1.6.3 2.4.3 1.7 0 3-.3 3.9-1 .9-.7 1.3-1.6 1.3-2.8 0-.9-.2-1.7-.7-2.2H20v-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (formatStrikethrough);


/***/ }),

/***/ "txjq":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ewfG");
/* harmony import */ var _gechiui_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__);
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

function Icon(_ref) {
  let {
    icon,
    size = 24,
    ...props
  } = _ref;
  return Object(_gechiui_element__WEBPACK_IMPORTED_MODULE_0__["cloneElement"])(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ __webpack_exports__["a"] = (Icon);


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