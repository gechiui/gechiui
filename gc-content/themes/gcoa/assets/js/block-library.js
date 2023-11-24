//js代码
//引入对应方法, 需要注意的是这里引用了4个方法, 那么在底部也需要window.gc.回调这4个方法
//这4个方法的来源是functions.php里的gc_register_script时array()里传入, 需要注意一一对应
(function (blocks, element, editor, i18n) {
    var el = element.createElement; //用于输出HTML
    var RichText = editor.RichText; //用于获取文本输入块
    blocks.registerBlockType('gutenberg-examples/example-03-editable', {
        apiVersion: 2,
        name: "core/gcoa-post-title",
        title: "文章标题",
        category: "theme",
        description: "显示文章、页面或任何其他内容类型的标题。",
        textdomain: "default",
        usesContext: ["postId", "postType", "queryId"],
        attributes: {
          textAlign: {
            type: "string"
          },
          level: {
            type: "number",
            "default": 2
          },
          isLink: {
            type: "boolean",
            "default": false
          },
          rel: {
            type: "string",
            attribute: "rel",
            "default": ""
          },
          linkTarget: {
            type: "string",
            "default": "_self"
          }
        },
        supports: {
          align: ["wide", "full"],
          html: false,
          color: {
            gradients: true,
            link: true
          },
          spacing: {
            margin: true
          },
          typography: {
            fontSize: true,
            lineHeight: true,
            __experimentalFontFamily: true,
            __experimentalFontWeight: true,
            __experimentalFontStyle: true,
            __experimentalTextTransform: true,
            __experimentalLetterSpacing: true,
            __experimentalDefaultControls: {
              fontSize: true,
              fontAppearance: true,
              textTransform: true
            }
          }
        },
        //编辑时
        edit: function (props) {
            return JSON.stringify(props);
            //获取模块输入的值
            // var content = props.attributes.content;
            //点击输入框时用的方法
            // function onChangeContent(newContent) {
            //     //将输入框里的内容输出到模块属性里
            //     props.setAttributes({ content: newContent });
            // }
            //返回HTML
            //el的方法格式为: el( 对象, 属性, 值 ); 可以相互嵌套
            //例如:
            // el(
            //     'div',
            //     {
            //         className: 'demo-class',
            //     },
            //     'DEMO数据'
            // );
            // 输出为: <div class="demo-class">DEMO数据</div>

            // return el(
            //     RichText,
            //     {
            //         tagName: 'p',
            //         className: props.className,
            //         onChange: onChangeContent,
            //         value: content,
            //     }
            // );
        },
        //保存时
        // save: function (props) {
        //     //保存时返回的HTML
        //     return el(RichText.Content, {
        //         tagName: 'p', value: props.attributes.content,
        //     });
        // },
    });
}(
    window.gc.blocks,
    window.gc.element,
    window.gc.editor,
    window.gc.i18n
));

// (function(module, __webpack_exports__, __webpack_require__) {
//   __webpack_require__.d(__webpack_exports__, "registerCoreBlocks", function() { return /* binding */ registerCoreBlocks; });
//   // NAMESPACE OBJECT: ./node_modules/@gechiui/block-library/build-module/post-title/index.js
//   var build_module_gcoa_post_title_namespaceObject = {};
//   __webpack_require__.r(build_module_gcoa_post_title_namespaceObject);
//   __webpack_require__.d(build_module_gcoa_post_title_namespaceObject, "metadata", function() { return gcoa_post_title_metadata; });
//   __webpack_require__.d(build_module_gcoa_post_title_namespaceObject, "name", function() { return gcoa_post_title_name; });
//   __webpack_require__.d(build_module_gcoa_post_title_namespaceObject, "settings", function() { return gcoa_post_title_settings; });






//   // CONCATENATED MODULE: ./node_modules/@gechiui/block-library/build-module/post-title/index.js
//   /**
//    * GeChiUI dependencies
//    */

//   /**
//    * Internal dependencies
//    */

//   const gcoa_post_title_metadata = {
//     apiVersion: 2,
//     name: "core/gcoa-post-title",
//     title: "文章标题",
//     category: "theme",
//     description: "显示文章、页面或任何其他内容类型的标题。",
//     textdomain: "default",
//     usesContext: ["postId", "postType", "queryId"],
//     attributes: {
//       textAlign: {
//         type: "string"
//       },
//       level: {
//         type: "number",
//         "default": 2
//       },
//       isLink: {
//         type: "boolean",
//         "default": false
//       },
//       rel: {
//         type: "string",
//         attribute: "rel",
//         "default": ""
//       },
//       linkTarget: {
//         type: "string",
//         "default": "_self"
//       }
//     },
//     supports: {
//       align: ["wide", "full"],
//       html: false,
//       color: {
//         gradients: true,
//         link: true
//       },
//       spacing: {
//         margin: true
//       },
//       typography: {
//         fontSize: true,
//         lineHeight: true,
//         __experimentalFontFamily: true,
//         __experimentalFontWeight: true,
//         __experimentalFontStyle: true,
//         __experimentalTextTransform: true,
//         __experimentalLetterSpacing: true,
//         __experimentalDefaultControls: {
//           fontSize: true,
//           fontAppearance: true,
//           textTransform: true
//         }
//       }
//     }
//   };


//   const {
//     name: gcoa_post_title_name
//   } = gcoa_post_title_metadata;

//   const gcoa_post_title_settings = {
//     icon: gcoa_post_title,
//     edit: PostTitleEdit,
//     deprecated: gcoa_post_title_deprecated
//   };

//   const __experimentalGetCoreBlocks = () => [build_module_gcoa_post_title_namespaceObject];

//   const registerCoreBlocks = function () {
//   let blocks = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : __experimentalGetCoreBlocks();
//   blocks.forEach(registerBlock);
//   Object(external_gc_blocks_["setDefaultBlockName"])(paragraph_name);

//   if (window.gc && window.gc.oldEditor) {
//     Object(external_gc_blocks_["setFreeformContentHandlerName"])(freeform_name);
//   }

//   Object(external_gc_blocks_["setUnregisteredTypeHandlerName"])(missing_name);
//   Object(external_gc_blocks_["setGroupingBlockName"])(group_name);
// };


