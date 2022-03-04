this["gc"] = this["gc"] || {}; this["gc"]["serverSideRender"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "lezw");
/******/ })
/************************************************************************/
/******/ ({

/***/ "IgLd":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["data"]; }());

/***/ }),

/***/ "JWwu":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["deprecated"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "dMTb":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["compose"]; }());

/***/ }),

/***/ "ewfG":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["element"]; }());

/***/ }),

/***/ "jd0n":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["components"]; }());

/***/ }),

/***/ "lezw":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["gc","element"]
var external_gc_element_ = __webpack_require__("ewfG");

// EXTERNAL MODULE: external ["gc","data"]
var external_gc_data_ = __webpack_require__("IgLd");

// EXTERNAL MODULE: external ["gc","deprecated"]
var external_gc_deprecated_ = __webpack_require__("JWwu");
var external_gc_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_gc_deprecated_);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["gc","compose"]
var external_gc_compose_ = __webpack_require__("dMTb");

// EXTERNAL MODULE: external ["gc","i18n"]
var external_gc_i18n_ = __webpack_require__("z4sU");

// EXTERNAL MODULE: external ["gc","apiFetch"]
var external_gc_apiFetch_ = __webpack_require__("xuem");
var external_gc_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_gc_apiFetch_);

// EXTERNAL MODULE: external ["gc","url"]
var external_gc_url_ = __webpack_require__("zP/e");

// EXTERNAL MODULE: external ["gc","components"]
var external_gc_components_ = __webpack_require__("jd0n");

// EXTERNAL MODULE: external ["gc","blocks"]
var external_gc_blocks_ = __webpack_require__("n68F");

// CONCATENATED MODULE: ./node_modules/@gechiui/server-side-render/build-module/server-side-render.js



/**
 * External dependencies
 */

/**
 * GeChiUI dependencies
 */








function rendererPath(block) {
  let attributes = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  let urlQueryArgs = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  return Object(external_gc_url_["addQueryArgs"])(`/gc/v2/block-renderer/${block}`, {
    context: 'edit',
    ...(null !== attributes ? {
      attributes
    } : {}),
    ...urlQueryArgs
  });
}

function DefaultEmptyResponsePlaceholder(_ref) {
  let {
    className
  } = _ref;
  return Object(external_gc_element_["createElement"])(external_gc_components_["Placeholder"], {
    className: className
  }, Object(external_gc_i18n_["__"])('区块渲染为空。'));
}

function DefaultErrorResponsePlaceholder(_ref2) {
  let {
    response,
    className
  } = _ref2;
  const errorMessage = Object(external_gc_i18n_["sprintf"])( // translators: %s: error message describing the problem
  Object(external_gc_i18n_["__"])('载入区块时发生错误：%s'), response.errorMsg);
  return Object(external_gc_element_["createElement"])(external_gc_components_["Placeholder"], {
    className: className
  }, errorMessage);
}

function DefaultLoadingResponsePlaceholder(_ref3) {
  let {
    children,
    showLoader
  } = _ref3;
  return Object(external_gc_element_["createElement"])("div", {
    style: {
      position: 'relative'
    }
  }, showLoader && Object(external_gc_element_["createElement"])("div", {
    style: {
      position: 'absolute',
      top: '50%',
      left: '50%',
      marginTop: '-9px',
      marginLeft: '-9px'
    }
  }, Object(external_gc_element_["createElement"])(external_gc_components_["Spinner"], null)), Object(external_gc_element_["createElement"])("div", {
    style: {
      opacity: showLoader ? '0.3' : 1
    }
  }, children));
}

function ServerSideRender(props) {
  const {
    attributes,
    block,
    className,
    httpMethod = 'GET',
    urlQueryArgs,
    EmptyResponsePlaceholder = DefaultEmptyResponsePlaceholder,
    ErrorResponsePlaceholder = DefaultErrorResponsePlaceholder,
    LoadingResponsePlaceholder = DefaultLoadingResponsePlaceholder
  } = props;
  const isMountedRef = Object(external_gc_element_["useRef"])(true);
  const [showLoader, setShowLoader] = Object(external_gc_element_["useState"])(false);
  const fetchRequestRef = Object(external_gc_element_["useRef"])();
  const [response, setResponse] = Object(external_gc_element_["useState"])(null);
  const prevProps = Object(external_gc_compose_["usePrevious"])(props);
  const [isLoading, setIsLoading] = Object(external_gc_element_["useState"])(false);

  function fetchData() {
    if (!isMountedRef.current) {
      return;
    }

    setIsLoading(true);

    const sanitizedAttributes = attributes && Object(external_gc_blocks_["__experimentalSanitizeBlockAttributes"])(block, attributes); // If httpMethod is 'POST', send the attributes in the request body instead of the URL.
    // This allows sending a larger attributes object than in a GET request, where the attributes are in the URL.


    const isPostRequest = 'POST' === httpMethod;
    const urlAttributes = isPostRequest ? null : sanitizedAttributes !== null && sanitizedAttributes !== void 0 ? sanitizedAttributes : null;
    const path = rendererPath(block, urlAttributes, urlQueryArgs);
    const data = isPostRequest ? {
      attributes: sanitizedAttributes !== null && sanitizedAttributes !== void 0 ? sanitizedAttributes : null
    } : null; // Store the latest fetch request so that when we process it, we can
    // check if it is the current request, to avoid race conditions on slow networks.

    const fetchRequest = fetchRequestRef.current = external_gc_apiFetch_default()({
      path,
      data,
      method: isPostRequest ? 'POST' : 'GET'
    }).then(fetchResponse => {
      if (isMountedRef.current && fetchRequest === fetchRequestRef.current && fetchResponse) {
        setResponse(fetchResponse.rendered);
      }
    }).catch(error => {
      if (isMountedRef.current && fetchRequest === fetchRequestRef.current) {
        setResponse({
          error: true,
          errorMsg: error.message
        });
      }
    }).finally(() => {
      if (isMountedRef.current && fetchRequest === fetchRequestRef.current) {
        setIsLoading(false);
      }
    });
    return fetchRequest;
  }

  const debouncedFetchData = Object(external_gc_compose_["useDebounce"])(fetchData, 500); // When the component unmounts, set isMountedRef to false. This will
  // let the async fetch callbacks know when to stop.

  Object(external_gc_element_["useEffect"])(() => () => {
    isMountedRef.current = false;
  }, []);
  Object(external_gc_element_["useEffect"])(() => {
    // Don't debounce the first fetch. This ensures that the first render
    // shows data as soon as possible
    if (prevProps === undefined) {
      fetchData();
    } else if (!Object(external_lodash_["isEqual"])(prevProps, props)) {
      debouncedFetchData();
    }
  });
  /**
   * Effect to handle showing the loading placeholder.
   * Show it only if there is no previous response or
   * the request takes more than one second.
   */

  Object(external_gc_element_["useEffect"])(() => {
    if (!isLoading) {
      return;
    }

    const timeout = setTimeout(() => {
      setShowLoader(true);
    }, 1000);
    return () => clearTimeout(timeout);
  }, [isLoading]);
  const hasResponse = !!response;
  const hasEmptyResponse = response === '';
  const hasError = response === null || response === void 0 ? void 0 : response.error;

  if (isLoading) {
    return Object(external_gc_element_["createElement"])(LoadingResponsePlaceholder, Object(esm_extends["a" /* default */])({}, props, {
      showLoader: showLoader
    }), hasResponse && Object(external_gc_element_["createElement"])(external_gc_element_["RawHTML"], {
      className: className
    }, response));
  }

  if (hasEmptyResponse || !hasResponse) {
    return Object(external_gc_element_["createElement"])(EmptyResponsePlaceholder, props);
  }

  if (hasError) {
    return Object(external_gc_element_["createElement"])(ErrorResponsePlaceholder, Object(esm_extends["a" /* default */])({
      response: response
    }, props));
  }

  return Object(external_gc_element_["createElement"])(external_gc_element_["RawHTML"], {
    className: className
  }, response);
}

// CONCATENATED MODULE: ./node_modules/@gechiui/server-side-render/build-module/index.js



/**
 * GeChiUI dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Constants
 */

const EMPTY_OBJECT = {};
const ExportedServerSideRender = Object(external_gc_data_["withSelect"])(select => {
  // FIXME: @gechiui/server-side-render should not depend on @gechiui/editor.
  // It is used by blocks that can be loaded into a *non-post* block editor.
  // eslint-disable-next-line @gechiui/data-no-store-string-literals
  const coreEditorSelect = select('core/editor');

  if (coreEditorSelect) {
    const currentPostId = coreEditorSelect.getCurrentPostId(); // For templates and template parts we use a custom ID format.
    // Since they aren't real posts, we don't want to use their ID
    // for server-side rendering. Since they use a string based ID,
    // we can assume real post IDs are numbers.

    if (currentPostId && typeof currentPostId === 'number') {
      return {
        currentPostId
      };
    }
  }

  return EMPTY_OBJECT;
})(_ref => {
  let {
    urlQueryArgs = EMPTY_OBJECT,
    currentPostId,
    ...props
  } = _ref;
  const newUrlQueryArgs = Object(external_gc_element_["useMemo"])(() => {
    if (!currentPostId) {
      return urlQueryArgs;
    }

    return {
      post_id: currentPostId,
      ...urlQueryArgs
    };
  }, [currentPostId, urlQueryArgs]);
  return Object(external_gc_element_["createElement"])(ServerSideRender, Object(esm_extends["a" /* default */])({
    urlQueryArgs: newUrlQueryArgs
  }, props));
});

if (window && window.gc && window.gc.components) {
  window.gc.components.ServerSideRender = Object(external_gc_element_["forwardRef"])((props, ref) => {
    external_gc_deprecated_default()('gc.components.ServerSideRender', {
      since: '5.3',
      alternative: 'gc.serverSideRender'
    });
    return Object(external_gc_element_["createElement"])(ExportedServerSideRender, Object(esm_extends["a" /* default */])({}, props, {
      ref: ref
    }));
  });
}

/* harmony default export */ var build_module = __webpack_exports__["default"] = (ExportedServerSideRender);


/***/ }),

/***/ "n68F":
/***/ (function(module, exports) {

(function() { module.exports = window["gc"]["blocks"]; }());

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

/******/ })["default"];