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
  "store": function() { return /* reexport */ store; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/notices/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "createErrorNotice": function() { return createErrorNotice; },
  "createInfoNotice": function() { return createInfoNotice; },
  "createNotice": function() { return createNotice; },
  "createSuccessNotice": function() { return createSuccessNotice; },
  "createWarningNotice": function() { return createWarningNotice; },
  "removeAllNotices": function() { return removeAllNotices; },
  "removeNotice": function() { return removeNotice; },
  "removeNotices": function() { return removeNotices; }
});

// NAMESPACE OBJECT: ./node_modules/@gechiui/notices/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getNotices": function() { return getNotices; }
});

;// CONCATENATED MODULE: external ["gc","data"]
var external_gc_data_namespaceObject = window["gc"]["data"];
;// CONCATENATED MODULE: ./node_modules/@gechiui/notices/build-module/store/utils/on-sub-key.js
/**
 * Higher-order reducer creator which creates a combined reducer object, keyed
 * by a property on the action object.
 *
 * @param {string} actionProperty Action property by which to key object.
 *
 * @return {Function} Higher-order reducer.
 */
const onSubKey = actionProperty => reducer => (state = {}, action) => {
  // Retrieve subkey from action. Do not track if undefined; useful for cases
  // where reducer is scoped by action shape.
  const key = action[actionProperty];

  if (key === undefined) {
    return state;
  } // Avoid updating state if unchanged. Note that this also accounts for a
  // reducer which returns undefined on a key which is not yet tracked.


  const nextKeyState = reducer(state[key], action);

  if (nextKeyState === state[key]) {
    return state;
  }

  return { ...state,
    [key]: nextKeyState
  };
};
/* harmony default export */ var on_sub_key = (onSubKey);

;// CONCATENATED MODULE: ./node_modules/@gechiui/notices/build-module/store/reducer.js
/**
 * Internal dependencies
 */

/**
 * Reducer returning the next notices state. The notices state is an object
 * where each key is a context, its value an array of notice objects.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const notices = on_sub_key('context')((state = [], action) => {
  switch (action.type) {
    case 'CREATE_NOTICE':
      // Avoid duplicates on ID.
      return [...state.filter(({
        id
      }) => id !== action.notice.id), action.notice];

    case 'REMOVE_NOTICE':
      return state.filter(({
        id
      }) => id !== action.id);

    case 'REMOVE_NOTICES':
      return state.filter(({
        id
      }) => !action.ids.includes(id));

    case 'REMOVE_ALL_NOTICES':
      return state.filter(({
        type
      }) => type !== action.noticeType);
  }

  return state;
});
/* harmony default export */ var reducer = (notices);

;// CONCATENATED MODULE: ./node_modules/@gechiui/notices/build-module/store/constants.js
/**
 * Default context to use for notice grouping when not otherwise specified. Its
 * specific value doesn't hold much meaning, but it must be reasonably unique
 * and, more importantly, referenced consistently in the store implementation.
 *
 * @type {string}
 */
const DEFAULT_CONTEXT = 'global';
/**
 * Default notice status.
 *
 * @type {string}
 */

const DEFAULT_STATUS = 'info';

;// CONCATENATED MODULE: ./node_modules/@gechiui/notices/build-module/store/actions.js
/**
 * Internal dependencies
 */

/**
 * @typedef {Object} GCNoticeAction Object describing a user action option associated with a notice.
 *
 * @property {string}    label   Message to use as action label.
 * @property {?string}   url     Optional URL of resource if action incurs
 *                               browser navigation.
 * @property {?Function} onClick Optional function to invoke when action is
 *                               triggered by user.
 *
 */

let uniqueId = 0;
/**
 * Returns an action object used in signalling that a notice is to be created.
 *
 * @param {string|undefined}      status                       Notice status ("info" if undefined is passed).
 * @param {string}                content                      Notice message.
 * @param {Object}                [options]                    Notice options.
 * @param {string}                [options.context='global']   Context under which to
 *                                                             group notice.
 * @param {string}                [options.id]                 Identifier for notice.
 *                                                             Automatically assigned
 *                                                             if not specified.
 * @param {boolean}               [options.isDismissible=true] Whether the notice can
 *                                                             be dismissed by user.
 * @param {string}                [options.type='default']     Type of notice, one of
 *                                                             `default`, or `snackbar`.
 * @param {boolean}               [options.speak=true]         Whether the notice
 *                                                             content should be
 *                                                             announced to screen
 *                                                             readers.
 * @param {Array<GCNoticeAction>} [options.actions]            User actions to be
 *                                                             presented with notice.
 * @param {string}                [options.icon]               An icon displayed with the notice.
 *                                                             Only used when type is set to `snackbar`.
 * @param {boolean}               [options.explicitDismiss]    Whether the notice includes
 *                                                             an explicit dismiss button and
 *                                                             can't be dismissed by clicking
 *                                                             the body of the notice. Only applies
 *                                                             when type is set to `snackbar`.
 * @param {Function}              [options.onDismiss]          Called when the notice is dismissed.
 *
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * const ExampleComponent = () => {
 *     const { createNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () => createNotice( 'success', __( '通知消息' ) ) }
 *         >
 *             { __( '生成成功通知！' ) }
 *         </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createNotice(status = DEFAULT_STATUS, content, options = {}) {
  const {
    speak = true,
    isDismissible = true,
    context = DEFAULT_CONTEXT,
    id = `${context}${++uniqueId}`,
    actions = [],
    type = 'default',
    __unstableHTML,
    icon = null,
    explicitDismiss = false,
    onDismiss
  } = options; // The supported value shape of content is currently limited to plain text
  // strings. To avoid setting expectation that e.g. a GCElement could be
  // supported, cast to a string.

  content = String(content);
  return {
    type: 'CREATE_NOTICE',
    context,
    notice: {
      id,
      status,
      content,
      spokenMessage: speak ? content : null,
      __unstableHTML,
      isDismissible,
      actions,
      type,
      icon,
      explicitDismiss,
      onDismiss
    }
  };
}
/**
 * Returns an action object used in signalling that a success notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * const ExampleComponent = () => {
 *     const { createSuccessNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                 createSuccessNotice( __( '成功！' ), {
 *                     type: 'snackbar',
 *                     icon: '🔥',
 *                 } )
 *             }
 *         >
 *             { __( '生成snackbar成功通知！' ) }
 *        </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createSuccessNotice(content, options) {
  return createNotice('success', content, options);
}
/**
 * Returns an action object used in signalling that an info notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * const ExampleComponent = () => {
 *     const { createInfoNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                createInfoNotice( __( '出事了！' ), {
 *                   isDismissible: false,
 *                } )
 *             }
 *         >
 *         { __( '生成无法驳回的通知。') }
 *       </Button>
 *       );
 * };
 *```
 *
 * @return {Object} Action object.
 */

function createInfoNotice(content, options) {
  return createNotice('info', content, options);
}
/**
 * Returns an action object used in signalling that an error notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * const ExampleComponent = () => {
 *     const { createErrorNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                 createErrorNotice( __( '发生错误！' ), {
 *                     type: 'snackbar',
 *                     explicitDismiss: true,
 *                 } )
 *             }
 *         >
 *             { __(
 *                 'Generate an snackbar error notice with explicit dismiss button.'
 *             ) }
 *         </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createErrorNotice(content, options) {
  return createNotice('error', content, options);
}
/**
 * Returns an action object used in signalling that a warning notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * const ExampleComponent = () => {
 *     const { createWarningNotice, createInfoNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                 createWarningNotice( __( '警告！'), {
 *                     onDismiss: () => {
 *                         createInfoNotice(
 *                             __( '警告已被撤销！'  )
 *                         );
 *                     },
 *                 } )
 *             }
 *         >
 *             { __( '使用onDismiss回调生成警告通知。' ) }
 *         </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createWarningNotice(content, options) {
  return createNotice('warning', content, options);
}
/**
 * Returns an action object used in signalling that a notice is to be removed.
 *
 * @param {string} id                 Notice unique identifier.
 * @param {string} [context='global'] Optional context (grouping) in which the notice is
 *                                    intended to appear. Defaults to default context.
 *
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * const ExampleComponent = () => {
 *    const notices = useSelect( ( select ) => select( noticesStore ).getNotices() );
 *    const { createWarningNotice, removeNotice } = useDispatch( noticesStore );
 *
 *    return (
 *         <>
 *             <Button
 *                 onClick={ () =>
 *                     createWarningNotice( __( '警告！'), {
 *                         isDismissible: false,
 *                     } )
 *                 }
 *             >
 *                 { __( '生成通知' ) }
 *             </Button>
 *             { notices.length > 0 && (
 *                 <Button onClick={ () => removeNotice( notices[ 0 ].id ) }>
 *                     { __( 'Remove the notice' ) }
 *                 </Button>
 *             ) }
 *         </>
 *     );
 *};
 * ```
 *
 * @return {Object} Action object.
 */

function removeNotice(id, context = DEFAULT_CONTEXT) {
  return {
    type: 'REMOVE_NOTICE',
    id,
    context
  };
}
/**
 * Removes all notices from a given context. Defaults to the default context.
 *
 * @param {string} noticeType The context to remove all notices from.
 * @param {string} context    The context to remove all notices from.
 *
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch, useSelect } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * export const ExampleComponent = () => {
 * 	const notices = useSelect( ( select ) =>
 * 		select( noticesStore ).getNotices()
 * 	);
 * 	const { removeNotices } = useDispatch( noticesStore );
 * 	return (
 * 		<>
 * 			<ul>
 * 				{ notices.map( ( notice ) => (
 * 					<li key={ notice.id }>{ notice.content }</li>
 * 				) ) }
 * 			</ul>
 * 			<Button
 * 				onClick={ () =>
 * 					removeAllNotices()
 * 				}
 * 			>
 * 				{ __( '清除所有通知', 'woo-gutenberg-products-block' ) }
 * 			</Button>
 * 			<Button
 * 				onClick={ () =>
 * 					removeAllNotices( 'snackbar' )
 * 				}
 * 			>
 * 				{ __( '清除所有snackbar通知', 'woo-gutenberg-products-block' ) }
 * 			</Button>
 * 		</>
 * 	);
 * };
 * ```
 *
 * @return {Object} 	   Action object.
 */

function removeAllNotices(noticeType = 'default', context = DEFAULT_CONTEXT) {
  return {
    type: 'REMOVE_ALL_NOTICES',
    noticeType,
    context
  };
}
/**
 * Returns an action object used in signalling that several notices are to be removed.
 *
 * @param {string[]} ids                List of unique notice identifiers.
 * @param {string}   [context='global'] Optional context (grouping) in which the notices are
 *                                      intended to appear. Defaults to default context.
 * @example
 * ```js
 * import { __ } from '@gechiui/i18n';
 * import { useDispatch, useSelect } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 * import { Button } from '@gechiui/components';
 *
 * const ExampleComponent = () => {
 * 	const notices = useSelect( ( select ) =>
 * 		select( noticesStore ).getNotices()
 * 	);
 * 	const { removeNotices } = useDispatch( noticesStore );
 * 	return (
 * 		<>
 * 			<ul>
 * 				{ notices.map( ( notice ) => (
 * 					<li key={ notice.id }>{ notice.content }</li>
 * 				) ) }
 * 			</ul>
 * 			<Button
 * 				onClick={ () =>
 * 					removeNotices( notices.map( ( { id } ) => id ) )
 * 				}
 * 			>
 * 				{ __( '清除所有通知' ) }
 * 			</Button>
 * 		</>
 * 	);
 * };
 * ```
 * @return {Object} Action object.
 */

function removeNotices(ids, context = DEFAULT_CONTEXT) {
  return {
    type: 'REMOVE_NOTICES',
    ids,
    context
  };
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/notices/build-module/store/selectors.js
/**
 * Internal dependencies
 */

/** @typedef {import('./actions').GCNoticeAction} GCNoticeAction */

/**
 * The default empty set of notices to return when there are no notices
 * assigned for a given notices context. This can occur if the getNotices
 * selector is called without a notice ever having been created for the
 * context. A shared value is used to ensure referential equality between
 * sequential selector calls, since otherwise `[] !== []`.
 *
 * @type {Array}
 */

const DEFAULT_NOTICES = [];
/**
 * @typedef {Object} GCNotice Notice object.
 *
 * @property {string}           id             Unique identifier of notice.
 * @property {string}           status         Status of notice, one of `success`,
 *                                             `info`, `error`, or `warning`. Defaults
 *                                             to `info`.
 * @property {string}           content        Notice message.
 * @property {string}           spokenMessage  Audibly announced message text used by
 *                                             assistive technologies.
 * @property {string}           __unstableHTML Notice message as raw HTML. Intended to
 *                                             serve primarily for compatibility of
 *                                             server-rendered notices, and SHOULD NOT
 *                                             be used for notices. It is subject to
 *                                             removal without notice.
 * @property {boolean}          isDismissible  Whether the notice can be dismissed by
 *                                             user. Defaults to `true`.
 * @property {string}           type           Type of notice, one of `default`,
 *                                             or `snackbar`. Defaults to `default`.
 * @property {boolean}          speak          Whether the notice content should be
 *                                             announced to screen readers. Defaults to
 *                                             `true`.
 * @property {GCNoticeAction[]} actions        User actions to present with notice.
 *
 */

/**
 * Returns all notices as an array, optionally for a given context. Defaults to
 * the global context.
 *
 * @param {Object}  state   Notices state.
 * @param {?string} context Optional grouping context.
 *
 * @example
 *
 *```js
 * import { useSelect } from '@gechiui/data';
 * import { store as noticesStore } from '@gechiui/notices';
 *
 * const ExampleComponent = () => {
 *     const notices = useSelect( ( select ) => select( noticesStore ).getNotices() );
 *     return (
 *         <ul>
 *         { notices.map( ( notice ) => (
 *             <li key={ notice.ID }>{ notice.content }</li>
 *         ) ) }
 *        </ul>
 *    )
 * };
 *```
 *
 * @return {GCNotice[]} Array of notices.
 */

function getNotices(state, context = DEFAULT_CONTEXT) {
  return state[context] || DEFAULT_NOTICES;
}

;// CONCATENATED MODULE: ./node_modules/@gechiui/notices/build-module/store/index.js
/**
 * GeChiUI dependencies
 */

/**
 * Internal dependencies
 */




/**
 * Store definition for the notices namespace.
 *
 * @see https://github.com/GeChiUI/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 */

const store = (0,external_gc_data_namespaceObject.createReduxStore)('core/notices', {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_gc_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@gechiui/notices/build-module/index.js


(window.gc = window.gc || {}).notices = __webpack_exports__;
/******/ })()
;