this["gc"] = this["gc"] || {}; this["gc"]["priorityQueue"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "+36o");
/******/ })
/************************************************************************/
/******/ ({

/***/ "+36o":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "createQueue", function() { return /* binding */ createQueue; });

// CONCATENATED MODULE: ./node_modules/@gechiui/priority-queue/build-module/request-idle-callback.js
/**
 * @typedef {( timeOrDeadline: IdleDeadline | number ) => void} Callback
 */

/**
 * @return {(callback: Callback) => void} RequestIdleCallback
 */
function createRequestIdleCallback() {
  if (typeof window === 'undefined') {
    return callback => {
      setTimeout(() => callback(Date.now()), 0);
    };
  }

  return window.requestIdleCallback || window.requestAnimationFrame;
}
/* harmony default export */ var request_idle_callback = (createRequestIdleCallback());

// CONCATENATED MODULE: ./node_modules/@gechiui/priority-queue/build-module/index.js
/**
 * Internal dependencies
 */

/**
 * Enqueued callback to invoke once idle time permits.
 *
 * @typedef {()=>void} GCPriorityQueueCallback
 */

/**
 * An object used to associate callbacks in a particular context grouping.
 *
 * @typedef {{}} GCPriorityQueueContext
 */

/**
 * Function to add callback to priority queue.
 *
 * @typedef {(element:GCPriorityQueueContext,item:GCPriorityQueueCallback)=>void} GCPriorityQueueAdd
 */

/**
 * Function to flush callbacks from priority queue.
 *
 * @typedef {(element:GCPriorityQueueContext)=>boolean} GCPriorityQueueFlush
 */

/**
 * Reset the queue.
 *
 * @typedef {()=>void} GCPriorityQueueReset
 */

/**
 * Priority queue instance.
 *
 * @typedef {Object} GCPriorityQueue
 *
 * @property {GCPriorityQueueAdd}   add   Add callback to queue for context.
 * @property {GCPriorityQueueFlush} flush Flush queue for context.
 * @property {GCPriorityQueueReset} reset Reset queue.
 */

/**
 * Creates a context-aware queue that only executes
 * the last task of a given context.
 *
 * @example
 *```js
 * import { createQueue } from '@gechiui/priority-queue';
 *
 * const queue = createQueue();
 *
 * // Context objects.
 * const ctx1 = {};
 * const ctx2 = {};
 *
 * // For a given context in the queue, only the last callback is executed.
 * queue.add( ctx1, () => console.log( 'This will be printed first' ) );
 * queue.add( ctx2, () => console.log( 'This won\'t be printed' ) );
 * queue.add( ctx2, () => console.log( 'This will be printed second' ) );
 *```
 *
 * @return {GCPriorityQueue} Queue object with `add`, `flush` and `reset` methods.
 */

const createQueue = () => {
  /** @type {GCPriorityQueueContext[]} */
  let waitingList = [];
  /** @type {WeakMap<GCPriorityQueueContext,GCPriorityQueueCallback>} */

  let elementsMap = new WeakMap();
  let isRunning = false;
  /**
   * Callback to process as much queue as time permits.
   *
   * @param {IdleDeadline|number} deadline Idle callback deadline object, or
   *                                       animation frame timestamp.
   */

  const runWaitingList = deadline => {
    const hasTimeRemaining = typeof deadline === 'number' ? () => false : () => deadline.timeRemaining() > 0;

    do {
      if (waitingList.length === 0) {
        isRunning = false;
        return;
      }

      const nextElement =
      /** @type {GCPriorityQueueContext} */
      waitingList.shift();
      const callback =
      /** @type {GCPriorityQueueCallback} */
      elementsMap.get(nextElement); // If errors with undefined callbacks are encountered double check that all of your useSelect calls
      // have all dependecies set correctly in second parameter. Missing dependencies can cause unexpected
      // loops and race conditions in the queue.

      callback();
      elementsMap.delete(nextElement);
    } while (hasTimeRemaining());

    request_idle_callback(runWaitingList);
  };
  /**
   * Add a callback to the queue for a given context.
   *
   * @type {GCPriorityQueueAdd}
   *
   * @param {GCPriorityQueueContext}  element Context object.
   * @param {GCPriorityQueueCallback} item    Callback function.
   */


  const add = (element, item) => {
    if (!elementsMap.has(element)) {
      waitingList.push(element);
    }

    elementsMap.set(element, item);

    if (!isRunning) {
      isRunning = true;
      request_idle_callback(runWaitingList);
    }
  };
  /**
   * Flushes queue for a given context, returning true if the flush was
   * performed, or false if there is no queue for the given context.
   *
   * @type {GCPriorityQueueFlush}
   *
   * @param {GCPriorityQueueContext} element Context object.
   *
   * @return {boolean} Whether flush was performed.
   */


  const flush = element => {
    if (!elementsMap.has(element)) {
      return false;
    }

    const index = waitingList.indexOf(element);
    waitingList.splice(index, 1);
    const callback =
    /** @type {GCPriorityQueueCallback} */
    elementsMap.get(element);
    elementsMap.delete(element);
    callback();
    return true;
  };
  /**
   * Reset the queue without running the pending callbacks.
   *
   * @type {GCPriorityQueueReset}
   */


  const reset = () => {
    waitingList = [];
    elementsMap = new WeakMap();
    isRunning = false;
  };

  return {
    add,
    flush,
    reset
  };
};


/***/ })

/******/ });