<?php return array(
  'archives' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/archives',
    'title' => '归档',
    'category' => 'widgets',
    'description' => '显示文章的日期归档。',
    'textdomain' => 'default',
    'attributes' => array(
      'displayAsDropdown' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'showLabel' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'showPostCounts' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'type' => array(
        'type' => 'string',
        'default' => 'monthly'
      )
    ),
    'supports' => array(
      'align' => true,
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-archives-editor'
  ),
  'audio' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/audio',
    'title' => '音频',
    'category' => 'media',
    'description' => '嵌入简单音频播放器。',
    'keywords' => array(
      'music',
      'sound',
      'podcast',
      'recording'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'src' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'audio',
        'attribute' => 'src',
        '__experimentalRole' => 'content'
      ),
      'caption' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'figcaption',
        '__experimentalRole' => 'content'
      ),
      'id' => array(
        'type' => 'number',
        '__experimentalRole' => 'content'
      ),
      'autoplay' => array(
        'type' => 'boolean',
        'source' => 'attribute',
        'selector' => 'audio',
        'attribute' => 'autoplay'
      ),
      'loop' => array(
        'type' => 'boolean',
        'source' => 'attribute',
        'selector' => 'audio',
        'attribute' => 'loop'
      ),
      'preload' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'audio',
        'attribute' => 'preload'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => true,
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      )
    ),
    'editorStyle' => 'gc-block-audio-editor',
    'style' => 'gc-block-audio'
  ),
  'avatar' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/avatar',
    'title' => '头像',
    'category' => 'theme',
    'description' => '添加一个用户的头像。',
    'textdomain' => 'default',
    'attributes' => array(
      'userId' => array(
        'type' => 'number'
      ),
      'size' => array(
        'type' => 'number',
        'default' => 96
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      )
    ),
    'usesContext' => array(
      'postType',
      'postId',
      'commentId'
    ),
    'supports' => array(
      'html' => false,
      'align' => true,
      'alignWide' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      '__experimentalBorder' => array(
        '__experimentalSkipSerialization' => true,
        'radius' => true,
        'width' => true,
        'color' => true,
        'style' => true,
        '__experimentalDefaultControls' => array(
          'radius' => true
        )
      ),
      'color' => array(
        'text' => false,
        'background' => false,
        '__experimentalDuotone' => 'img'
      )
    ),
    'editorStyle' => 'gc-block-avatar-editor',
    'style' => 'gc-block-avatar'
  ),
  'block' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/block',
    'title' => '区块样板',
    'category' => 'reusable',
    'description' => '创建并保存内容以在您的系统上重复使用。更新该区块后，这些变更将应用​​至所有使用该区块的位置。',
    'keywords' => array(
      'reusable'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'ref' => array(
        'type' => 'number'
      )
    ),
    'supports' => array(
      'customClassName' => false,
      'html' => false,
      'inserter' => false
    )
  ),
  'button' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/button',
    'title' => '按钮',
    'category' => 'design',
    'parent' => array(
      'core/buttons'
    ),
    'description' => '通过按钮式的链接来提示访客进行操作。',
    'keywords' => array(
      'link'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'url' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'a',
        'attribute' => 'href',
        '__experimentalRole' => 'content'
      ),
      'title' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'a',
        'attribute' => 'title',
        '__experimentalRole' => 'content'
      ),
      'text' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'a',
        '__experimentalRole' => 'content'
      ),
      'linkTarget' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'a',
        'attribute' => 'target',
        '__experimentalRole' => 'content'
      ),
      'rel' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'a',
        'attribute' => 'rel',
        '__experimentalRole' => 'content'
      ),
      'placeholder' => array(
        'type' => 'string'
      ),
      'backgroundColor' => array(
        'type' => 'string'
      ),
      'textColor' => array(
        'type' => 'string'
      ),
      'gradient' => array(
        'type' => 'string'
      ),
      'width' => array(
        'type' => 'number'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => false,
      'alignWide' => false,
      'color' => array(
        '__experimentalSkipSerialization' => true,
        'gradients' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'reusable' => false,
      'shadow' => true,
      'spacing' => array(
        '__experimentalSkipSerialization' => true,
        'padding' => array(
          'horizontal',
          'vertical'
        ),
        '__experimentalDefaultControls' => array(
          'padding' => true
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'style' => true,
        'width' => true,
        '__experimentalSkipSerialization' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'style' => true,
          'width' => true
        )
      ),
      '__experimentalSelector' => '.gc-block-button .gc-block-button__link'
    ),
    'styles' => array(
      array(
        'name' => 'fill',
        'label' => 'Fill',
        'isDefault' => true
      ),
      array(
        'name' => 'outline',
        'label' => 'Outline'
      )
    ),
    'editorStyle' => 'gc-block-button-editor',
    'style' => 'gc-block-button'
  ),
  'buttons' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/buttons',
    'title' => '多个按钮',
    'category' => 'design',
    'description' => '使用一组按钮式的链接来提示访客进行操作。',
    'keywords' => array(
      'link'
    ),
    'textdomain' => 'default',
    'supports' => array(
      'anchor' => true,
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      '__experimentalExposeControlsToChildren' => true,
      'spacing' => array(
        'blockGap' => true,
        'margin' => array(
          'top',
          'bottom'
        ),
        '__experimentalDefaultControls' => array(
          'blockGap' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'layout' => array(
        'allowSwitching' => false,
        'allowInheriting' => false,
        'default' => array(
          'type' => 'flex'
        )
      )
    ),
    'editorStyle' => 'gc-block-buttons-editor',
    'style' => 'gc-block-buttons'
  ),
  'calendar' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/calendar',
    'title' => '日历',
    'category' => 'widgets',
    'description' => '您文章的日历。',
    'keywords' => array(
      'posts',
      'archive'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'month' => array(
        'type' => 'integer'
      ),
      'year' => array(
        'type' => 'integer'
      )
    ),
    'supports' => array(
      'align' => true,
      'color' => array(
        'link' => true,
        '__experimentalSkipSerialization' => array(
          'text',
          'background'
        ),
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        ),
        '__experimentalSelector' => 'table, th'
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'style' => 'gc-block-calendar'
  ),
  'categories' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/categories',
    'title' => '分类列表',
    'category' => 'widgets',
    'description' => '显示所有分类的列表。',
    'textdomain' => 'default',
    'attributes' => array(
      'displayAsDropdown' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'showHierarchy' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'showPostCounts' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'showOnlyTopLevel' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'showEmpty' => array(
        'type' => 'boolean',
        'default' => false
      )
    ),
    'supports' => array(
      'align' => true,
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-categories-editor',
    'style' => 'gc-block-categories'
  ),
  'code' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/code',
    'title' => '代码',
    'category' => 'text',
    'description' => '显示符合间距和制表符的代码片段。',
    'textdomain' => 'default',
    'attributes' => array(
      'content' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'code'
      )
    ),
    'supports' => array(
      'align' => array(
        'wide'
      ),
      'anchor' => true,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'spacing' => array(
        'margin' => array(
          'top',
          'bottom'
        ),
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      '__experimentalBorder' => array(
        'radius' => true,
        'color' => true,
        'width' => true,
        'style' => true,
        '__experimentalDefaultControls' => array(
          'width' => true,
          'color' => true
        )
      ),
      'color' => array(
        'text' => true,
        'background' => true,
        'gradients' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      )
    ),
    'style' => 'gc-block-code'
  ),
  'column' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/column',
    'title' => '栏目',
    'category' => 'design',
    'parent' => array(
      'core/columns'
    ),
    'description' => '多栏区块中的一栏。',
    'textdomain' => 'default',
    'attributes' => array(
      'verticalAlignment' => array(
        'type' => 'string'
      ),
      'width' => array(
        'type' => 'string'
      ),
      'allowedBlocks' => array(
        'type' => 'array'
      ),
      'templateLock' => array(
        'type' => array(
          'string',
          'boolean'
        ),
        'enum' => array(
          'all',
          'insert',
          'contentOnly',
          false
        )
      )
    ),
    'supports' => array(
      'anchor' => true,
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'blockGap' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'padding' => true,
          'blockGap' => true
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'style' => true,
        'width' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'style' => true,
          'width' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'layout' => true
    )
  ),
  'columns' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/columns',
    'title' => '栏目',
    'category' => 'design',
    'description' => '在多列中显示内容，并在每列中添加区块。',
    'textdomain' => 'default',
    'attributes' => array(
      'verticalAlignment' => array(
        'type' => 'string'
      ),
      'isStackedOnMobile' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'templateLock' => array(
        'type' => array(
          'string',
          'boolean'
        ),
        'enum' => array(
          'all',
          'insert',
          'contentOnly',
          false
        )
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'blockGap' => array(
          '__experimentalDefault' => '2em',
          'sides' => array(
            'horizontal',
            'vertical'
          )
        ),
        'margin' => array(
          'top',
          'bottom'
        ),
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'padding' => true,
          'blockGap' => true
        )
      ),
      'layout' => array(
        'allowSwitching' => false,
        'allowInheriting' => false,
        'allowEditing' => false,
        'default' => array(
          'type' => 'flex',
          'flexWrap' => 'nowrap'
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'style' => true,
        'width' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'style' => true,
          'width' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-columns-editor',
    'style' => 'gc-block-columns'
  ),
  'comment-author-name' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comment-author-name',
    'title' => '评论者名称',
    'category' => 'theme',
    'ancestor' => array(
      'core/comment-template'
    ),
    'description' => '显示评论者的名称。',
    'textdomain' => 'default',
    'attributes' => array(
      'isLink' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      ),
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'commentId'
    ),
    'supports' => array(
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'comment-content' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comment-content',
    'title' => '评论内容',
    'category' => 'theme',
    'ancestor' => array(
      'core/comment-template'
    ),
    'description' => '显示评论的内容。',
    'textdomain' => 'default',
    'usesContext' => array(
      'commentId'
    ),
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'spacing' => array(
        'padding' => array(
          'horizontal',
          'vertical'
        ),
        '__experimentalDefaultControls' => array(
          'padding' => true
        )
      ),
      'html' => false
    )
  ),
  'comment-date' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comment-date',
    'title' => '评论日期',
    'category' => 'theme',
    'ancestor' => array(
      'core/comment-template'
    ),
    'description' => '显示评论发表的日期。',
    'textdomain' => 'default',
    'attributes' => array(
      'format' => array(
        'type' => 'string'
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => true
      )
    ),
    'usesContext' => array(
      'commentId'
    ),
    'supports' => array(
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'comment-edit-link' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comment-edit-link',
    'title' => '评论编辑链接',
    'category' => 'theme',
    'ancestor' => array(
      'core/comment-template'
    ),
    'description' => '显示一个通向 GeChiUI 仪表盘中编辑评论的链接。此链接仅对于具有编辑权限的用户可见。',
    'textdomain' => 'default',
    'usesContext' => array(
      'commentId'
    ),
    'attributes' => array(
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      ),
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'html' => false,
      'color' => array(
        'link' => true,
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'comment-reply-link' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comment-reply-link',
    'title' => '评论回复链接',
    'category' => 'theme',
    'ancestor' => array(
      'core/comment-template'
    ),
    'description' => '显示用于回复评论的链接。',
    'textdomain' => 'default',
    'usesContext' => array(
      'commentId'
    ),
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'color' => array(
        'gradients' => true,
        'link' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'html' => false
    )
  ),
  'comment-template' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comment-template',
    'title' => '评论模板',
    'category' => 'design',
    'parent' => array(
      'core/comments'
    ),
    'description' => '包含用于显示评论的区块元素，例如标题、日期、作者、头像等。',
    'textdomain' => 'default',
    'usesContext' => array(
      'postId'
    ),
    'supports' => array(
      'align' => true,
      'html' => false,
      'reusable' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'style' => 'gc-block-comment-template'
  ),
  'comments' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comments',
    'title' => '评论',
    'category' => 'theme',
    'description' => '一个允许使用不同视觉配置显示评论的高级区块。',
    'textdomain' => 'default',
    'attributes' => array(
      'tagName' => array(
        'type' => 'string',
        'default' => 'div'
      ),
      'legacy' => array(
        'type' => 'boolean',
        'default' => false
      )
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-comments-editor',
    'usesContext' => array(
      'postId',
      'postType'
    )
  ),
  'comments-pagination' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comments-pagination',
    'title' => '评论分页',
    'category' => 'theme',
    'parent' => array(
      'core/comments'
    ),
    'description' => '如果适用，显示下一组/上一组评论的分页导航。',
    'textdomain' => 'default',
    'attributes' => array(
      'paginationArrow' => array(
        'type' => 'string',
        'default' => 'none'
      )
    ),
    'providesContext' => array(
      'comments/paginationArrow' => 'paginationArrow'
    ),
    'supports' => array(
      'align' => true,
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'layout' => array(
        'allowSwitching' => false,
        'allowInheriting' => false,
        'default' => array(
          'type' => 'flex'
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-comments-pagination-editor',
    'style' => 'gc-block-comments-pagination'
  ),
  'comments-pagination-next' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comments-pagination-next',
    'title' => '评论下一页',
    'category' => 'theme',
    'parent' => array(
      'core/comments-pagination'
    ),
    'description' => '显示下一页评论的页面链接。',
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'postId',
      'comments/paginationArrow'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'comments-pagination-numbers' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comments-pagination-numbers',
    'title' => '评论分页页码',
    'category' => 'theme',
    'parent' => array(
      'core/comments-pagination'
    ),
    'description' => '显示评论分页的页码列表。',
    'textdomain' => 'default',
    'usesContext' => array(
      'postId'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'comments-pagination-previous' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comments-pagination-previous',
    'title' => '评论上一页',
    'category' => 'theme',
    'parent' => array(
      'core/comments-pagination'
    ),
    'description' => '显示上一页评论的页面链接。',
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'postId',
      'comments/paginationArrow'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'comments-title' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/comments-title',
    'title' => '评论标题',
    'category' => 'theme',
    'ancestor' => array(
      'core/comments'
    ),
    'description' => '显示包含评论数量的标题',
    'textdomain' => 'default',
    'usesContext' => array(
      'postId',
      'postType'
    ),
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'showPostTitle' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'showCommentsCount' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'level' => array(
        'type' => 'number',
        'default' => 2
      )
    ),
    'supports' => array(
      'anchor' => false,
      'align' => true,
      'html' => false,
      '__experimentalBorder' => array(
        'radius' => true,
        'color' => true,
        'width' => true,
        'style' => true
      ),
      'color' => array(
        'gradients' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          '__experimentalFontFamily' => true,
          '__experimentalFontStyle' => true,
          '__experimentalFontWeight' => true
        )
      )
    )
  ),
  'cover' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/cover',
    'title' => '封面',
    'category' => 'media',
    'description' => '添加带有文本叠加层的图片或视频。',
    'textdomain' => 'default',
    'attributes' => array(
      'url' => array(
        'type' => 'string'
      ),
      'useFeaturedImage' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'id' => array(
        'type' => 'number'
      ),
      'alt' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'img',
        'attribute' => 'alt',
        'default' => ''
      ),
      'hasParallax' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'isRepeated' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'dimRatio' => array(
        'type' => 'number',
        'default' => 100
      ),
      'overlayColor' => array(
        'type' => 'string'
      ),
      'customOverlayColor' => array(
        'type' => 'string'
      ),
      'backgroundType' => array(
        'type' => 'string',
        'default' => 'image'
      ),
      'focalPoint' => array(
        'type' => 'object'
      ),
      'minHeight' => array(
        'type' => 'number'
      ),
      'minHeightUnit' => array(
        'type' => 'string'
      ),
      'gradient' => array(
        'type' => 'string'
      ),
      'customGradient' => array(
        'type' => 'string'
      ),
      'contentPosition' => array(
        'type' => 'string'
      ),
      'isDark' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'allowedBlocks' => array(
        'type' => 'array'
      ),
      'templateLock' => array(
        'type' => array(
          'string',
          'boolean'
        ),
        'enum' => array(
          'all',
          'insert',
          'contentOnly',
          false
        )
      ),
      'tagName' => array(
        'type' => 'string',
        'default' => 'div'
      )
    ),
    'usesContext' => array(
      'postId',
      'postType'
    ),
    'supports' => array(
      'anchor' => true,
      'align' => true,
      'html' => false,
      'spacing' => array(
        'padding' => true,
        'margin' => array(
          'top',
          'bottom'
        ),
        'blockGap' => true,
        '__experimentalDefaultControls' => array(
          'padding' => true,
          'blockGap' => true
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'style' => true,
        'width' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'style' => true,
          'width' => true
        )
      ),
      'color' => array(
        '__experimentalDuotone' => '> .gc-block-cover__image-background, > .gc-block-cover__video-background',
        'text' => true,
        'background' => false,
        '__experimentalSkipSerialization' => array(
          'gradients'
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'layout' => array(
        'allowJustification' => false
      )
    ),
    'editorStyle' => 'gc-block-cover-editor',
    'style' => 'gc-block-cover'
  ),
  'details' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/details',
    'title' => '详细信息',
    'category' => 'text',
    'description' => '隐藏与显示更多内容',
    'keywords' => array(
      'disclosure',
      'summary',
      'hide'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'showContent' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'summary' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'width' => true,
        'style' => true
      ),
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-details-editor',
    'style' => 'gc-block-details'
  ),
  'embed' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/embed',
    'title' => '嵌入',
    'category' => 'embed',
    'description' => '添加可显示抖音、优酷等系统嵌入内容的区块。',
    'textdomain' => 'default',
    'attributes' => array(
      'url' => array(
        'type' => 'string',
        '__experimentalRole' => 'content'
      ),
      'caption' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'figcaption',
        '__experimentalRole' => 'content'
      ),
      'type' => array(
        'type' => 'string',
        '__experimentalRole' => 'content'
      ),
      'providerNameSlug' => array(
        'type' => 'string',
        '__experimentalRole' => 'content'
      ),
      'allowResponsive' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'responsive' => array(
        'type' => 'boolean',
        'default' => false,
        '__experimentalRole' => 'content'
      ),
      'previewable' => array(
        'type' => 'boolean',
        'default' => true,
        '__experimentalRole' => 'content'
      )
    ),
    'supports' => array(
      'align' => true,
      'spacing' => array(
        'margin' => true
      )
    ),
    'editorStyle' => 'gc-block-embed-editor',
    'style' => 'gc-block-embed'
  ),
  'file' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/file',
    'title' => '文件',
    'category' => 'media',
    'description' => '添加指向可下载文件的链接。',
    'keywords' => array(
      'document',
      'pdf',
      'download'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'id' => array(
        'type' => 'number'
      ),
      'href' => array(
        'type' => 'string'
      ),
      'fileId' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'a:not([download])',
        'attribute' => 'id'
      ),
      'fileName' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'a:not([download])'
      ),
      'textLinkHref' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'a:not([download])',
        'attribute' => 'href'
      ),
      'textLinkTarget' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'a:not([download])',
        'attribute' => 'target'
      ),
      'showDownloadButton' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'downloadButtonText' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'a[download]'
      ),
      'displayPreview' => array(
        'type' => 'boolean'
      ),
      'previewHeight' => array(
        'type' => 'number',
        'default' => 600
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => true,
      'color' => array(
        'gradients' => true,
        'link' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'link' => true
        )
      )
    ),
    'viewScript' => 'file:./view.min.js',
    'editorStyle' => 'gc-block-file-editor',
    'style' => 'gc-block-file'
  ),
  'footnotes' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/footnotes',
    'title' => '脚注',
    'category' => 'text',
    'description' => '',
    'keywords' => array(
      'references'
    ),
    'textdomain' => 'default',
    'usesContext' => array(
      'postId',
      'postType'
    ),
    'supports' => array(
      'html' => false,
      'multiple' => false,
      'reusable' => false
    ),
    'style' => 'gc-block-footnotes'
  ),
  'freeform' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/freeform',
    'title' => '经典',
    'category' => 'text',
    'description' => '使用经典GeChiUI编辑器。',
    'textdomain' => 'default',
    'attributes' => array(
      'content' => array(
        'type' => 'string',
        'source' => 'raw'
      )
    ),
    'supports' => array(
      'className' => false,
      'customClassName' => false,
      'reusable' => false
    ),
    'editorStyle' => 'gc-block-freeform-editor'
  ),
  'gallery' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/gallery',
    'title' => '图库',
    'category' => 'media',
    'description' => '在相册中展示多张图片。',
    'keywords' => array(
      'images',
      'photos'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'images' => array(
        'type' => 'array',
        'default' => array(
          
        ),
        'source' => 'query',
        'selector' => '.blocks-gallery-item',
        'query' => array(
          'url' => array(
            'type' => 'string',
            'source' => 'attribute',
            'selector' => 'img',
            'attribute' => 'src'
          ),
          'fullUrl' => array(
            'type' => 'string',
            'source' => 'attribute',
            'selector' => 'img',
            'attribute' => 'data-full-url'
          ),
          'link' => array(
            'type' => 'string',
            'source' => 'attribute',
            'selector' => 'img',
            'attribute' => 'data-link'
          ),
          'alt' => array(
            'type' => 'string',
            'source' => 'attribute',
            'selector' => 'img',
            'attribute' => 'alt',
            'default' => ''
          ),
          'id' => array(
            'type' => 'string',
            'source' => 'attribute',
            'selector' => 'img',
            'attribute' => 'data-id'
          ),
          'caption' => array(
            'type' => 'string',
            'source' => 'html',
            'selector' => '.blocks-gallery-item__caption'
          )
        )
      ),
      'ids' => array(
        'type' => 'array',
        'items' => array(
          'type' => 'number'
        ),
        'default' => array(
          
        )
      ),
      'shortCodeTransforms' => array(
        'type' => 'array',
        'default' => array(
          
        ),
        'items' => array(
          'type' => 'object'
        )
      ),
      'columns' => array(
        'type' => 'number',
        'minimum' => 1,
        'maximum' => 8
      ),
      'caption' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => '.blocks-gallery-caption'
      ),
      'imageCrop' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'fixedHeight' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'linkTarget' => array(
        'type' => 'string'
      ),
      'linkTo' => array(
        'type' => 'string'
      ),
      'sizeSlug' => array(
        'type' => 'string',
        'default' => 'large'
      ),
      'allowResize' => array(
        'type' => 'boolean',
        'default' => false
      )
    ),
    'providesContext' => array(
      'allowResize' => 'allowResize',
      'imageCrop' => 'imageCrop',
      'fixedHeight' => 'fixedHeight'
    ),
    'supports' => array(
      'anchor' => true,
      'align' => true,
      'html' => false,
      'units' => array(
        'px',
        'em',
        'rem',
        'vh',
        'vw'
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        'blockGap' => array(
          'horizontal',
          'vertical'
        ),
        '__experimentalSkipSerialization' => array(
          'blockGap'
        ),
        '__experimentalDefaultControls' => array(
          'blockGap' => true,
          'margin' => false,
          'padding' => false
        )
      ),
      'color' => array(
        'text' => false,
        'background' => true,
        'gradients' => true
      ),
      'layout' => array(
        'allowSwitching' => false,
        'allowInheriting' => false,
        'allowEditing' => false,
        'default' => array(
          'type' => 'flex'
        )
      )
    ),
    'editorStyle' => 'gc-block-gallery-editor',
    'style' => 'gc-block-gallery'
  ),
  'group' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/group',
    'title' => '组',
    'category' => 'design',
    'description' => '在布局容器里收集区块。',
    'keywords' => array(
      'container',
      'wrapper',
      'row',
      'section'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'tagName' => array(
        'type' => 'string',
        'default' => 'div'
      ),
      'templateLock' => array(
        'type' => array(
          'string',
          'boolean'
        ),
        'enum' => array(
          'all',
          'insert',
          'contentOnly',
          false
        )
      ),
      'allowedBlocks' => array(
        'type' => 'array'
      )
    ),
    'supports' => array(
      '__experimentalOnEnter' => true,
      '__experimentalSettings' => true,
      'align' => array(
        'wide',
        'full'
      ),
      'anchor' => true,
      'ariaLabel' => true,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => array(
          'top',
          'bottom'
        ),
        'padding' => true,
        'blockGap' => true,
        '__experimentalDefaultControls' => array(
          'padding' => true,
          'blockGap' => true
        )
      ),
      'dimensions' => array(
        'minHeight' => true
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'style' => true,
        'width' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'style' => true,
          'width' => true
        )
      ),
      'position' => array(
        'sticky' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'layout' => array(
        'allowSizingOnChildren' => true
      )
    ),
    'editorStyle' => 'gc-block-group-editor',
    'style' => 'gc-block-group'
  ),
  'heading' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/heading',
    'title' => '标题',
    'category' => 'text',
    'description' => '介绍新章节并组织内容，以帮助访问者和搜索引擎了解您的内容结构。',
    'keywords' => array(
      'title',
      'subtitle'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'content' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'h1,h2,h3,h4,h5,h6',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'level' => array(
        'type' => 'number',
        'default' => 2
      ),
      'placeholder' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'anchor' => true,
      'className' => true,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'fontAppearance' => true,
          'textTransform' => true
        )
      ),
      '__unstablePasteTextInline' => true,
      '__experimentalSlashInserter' => true
    ),
    'editorStyle' => 'gc-block-heading-editor',
    'style' => 'gc-block-heading'
  ),
  'home-link' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/home-link',
    'category' => 'design',
    'parent' => array(
      'core/navigation'
    ),
    'title' => '首页链接',
    'description' => '创建一个始终指向系统主页的链接。如果在标题中已经存在系统标题链接，通常没有必要。',
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'textColor',
      'customTextColor',
      'backgroundColor',
      'customBackgroundColor',
      'fontSize',
      'customFontSize',
      'style'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-home-link-editor',
    'style' => 'gc-block-home-link'
  ),
  'html' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/html',
    'title' => '自定义HTML',
    'category' => 'widgets',
    'description' => '添加自定义 HTML 代码并在编辑时进行预览。',
    'keywords' => array(
      'embed'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'content' => array(
        'type' => 'string',
        'source' => 'raw'
      )
    ),
    'supports' => array(
      'customClassName' => false,
      'className' => false,
      'html' => false
    ),
    'editorStyle' => 'gc-block-html-editor'
  ),
  'image' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/image',
    'title' => '图片',
    'category' => 'media',
    'usesContext' => array(
      'allowResize',
      'imageCrop',
      'fixedHeight'
    ),
    'description' => '插入图片用于视觉说明。',
    'keywords' => array(
      'img',
      'photo',
      'picture'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'align' => array(
        'type' => 'string'
      ),
      'url' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'img',
        'attribute' => 'src',
        '__experimentalRole' => 'content'
      ),
      'alt' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'img',
        'attribute' => 'alt',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'caption' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'figcaption',
        '__experimentalRole' => 'content'
      ),
      'title' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'img',
        'attribute' => 'title',
        '__experimentalRole' => 'content'
      ),
      'href' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure > a',
        'attribute' => 'href',
        '__experimentalRole' => 'content'
      ),
      'rel' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure > a',
        'attribute' => 'rel'
      ),
      'linkClass' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure > a',
        'attribute' => 'class'
      ),
      'id' => array(
        'type' => 'number',
        '__experimentalRole' => 'content'
      ),
      'width' => array(
        'type' => 'number'
      ),
      'height' => array(
        'type' => 'number'
      ),
      'aspectRatio' => array(
        'type' => 'string'
      ),
      'scale' => array(
        'type' => 'string'
      ),
      'sizeSlug' => array(
        'type' => 'string'
      ),
      'linkDestination' => array(
        'type' => 'string'
      ),
      'linkTarget' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure > a',
        'attribute' => 'target'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'behaviors' => array(
        'lightbox' => true
      ),
      'color' => array(
        'text' => false,
        'background' => false
      ),
      'filter' => array(
        'duotone' => true
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'width' => true,
        '__experimentalSkipSerialization' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'width' => true
        )
      )
    ),
    'selectors' => array(
      'border' => '.gc-block-image img, .gc-block-image .gc-block-image__crop-area, .gc-block-image .components-placeholder',
      'filter' => array(
        'duotone' => '.gc-block-image img, .gc-block-image .components-placeholder'
      )
    ),
    'styles' => array(
      array(
        'name' => 'default',
        'label' => 'Default',
        'isDefault' => true
      ),
      array(
        'name' => 'rounded',
        'label' => 'Rounded'
      )
    ),
    'editorStyle' => 'gc-block-image-editor',
    'style' => 'gc-block-image'
  ),
  'latest-comments' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/latest-comments',
    'title' => '最新评论',
    'category' => 'widgets',
    'description' => '显示您的最近评论的列表。',
    'keywords' => array(
      '近期评论'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'commentsToShow' => array(
        'type' => 'number',
        'default' => 5,
        'minimum' => 1,
        'maximum' => 100
      ),
      'displayAvatar' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'displayDate' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'displayExcerpt' => array(
        'type' => 'boolean',
        'default' => true
      )
    ),
    'supports' => array(
      'align' => true,
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-latest-comments-editor',
    'style' => 'gc-block-latest-comments'
  ),
  'latest-posts' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/latest-posts',
    'title' => '最新文章',
    'category' => 'widgets',
    'description' => '显示您的近期文章的列表。',
    'keywords' => array(
      '近期文章'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'categories' => array(
        'type' => 'array',
        'items' => array(
          'type' => 'object'
        )
      ),
      'selectedAuthor' => array(
        'type' => 'number'
      ),
      'postsToShow' => array(
        'type' => 'number',
        'default' => 5
      ),
      'displayPostContent' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'displayPostContentRadio' => array(
        'type' => 'string',
        'default' => 'excerpt'
      ),
      'excerptLength' => array(
        'type' => 'number',
        'default' => 55
      ),
      'displayAuthor' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'displayPostDate' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'postLayout' => array(
        'type' => 'string',
        'default' => 'list'
      ),
      'columns' => array(
        'type' => 'number',
        'default' => 3
      ),
      'order' => array(
        'type' => 'string',
        'default' => 'desc'
      ),
      'orderBy' => array(
        'type' => 'string',
        'default' => 'date'
      ),
      'displayFeaturedImage' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'featuredImageAlign' => array(
        'type' => 'string',
        'enum' => array(
          'left',
          'center',
          'right'
        )
      ),
      'featuredImageSizeSlug' => array(
        'type' => 'string',
        'default' => 'thumbnail'
      ),
      'featuredImageSizeWidth' => array(
        'type' => 'number',
        'default' => null
      ),
      'featuredImageSizeHeight' => array(
        'type' => 'number',
        'default' => null
      ),
      'addLinkToFeaturedImage' => array(
        'type' => 'boolean',
        'default' => false
      )
    ),
    'supports' => array(
      'align' => true,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-latest-posts-editor',
    'style' => 'gc-block-latest-posts'
  ),
  'legacy-widget' => array(
    'apiVersion' => 3,
    'name' => 'core/legacy-widget',
    'title' => '旧版小工具',
    'category' => 'widgets',
    'description' => '显示旧版的小工具。',
    'textdomain' => 'default',
    'attributes' => array(
      'id' => array(
        'type' => 'string',
        'default' => null
      ),
      'idBase' => array(
        'type' => 'string',
        'default' => null
      ),
      'instance' => array(
        'type' => 'object',
        'default' => null
      )
    ),
    'supports' => array(
      'html' => false,
      'customClassName' => false,
      'reusable' => false
    ),
    'editorStyle' => 'gc-block-legacy-widget-editor'
  ),
  'list' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/list',
    'title' => '列表',
    'category' => 'text',
    'description' => '创建项目符号或编号列表。',
    'keywords' => array(
      '项目符号列表',
      '有序列表',
      '编号列表'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'ordered' => array(
        'type' => 'boolean',
        'default' => false,
        '__experimentalRole' => 'content'
      ),
      'values' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'ol,ul',
        'multiline' => 'li',
        '__unstableMultilineWrapperTags' => array(
          'ol',
          'ul'
        ),
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'type' => array(
        'type' => 'string'
      ),
      'start' => array(
        'type' => 'number'
      ),
      'reversed' => array(
        'type' => 'boolean'
      ),
      'placeholder' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'className' => false,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      '__unstablePasteTextInline' => true,
      '__experimentalSelector' => 'ol,ul',
      '__experimentalSlashInserter' => true
    ),
    'editorStyle' => 'gc-block-list-editor',
    'style' => 'gc-block-list'
  ),
  'list-item' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/list-item',
    'title' => '列表项目',
    'category' => 'text',
    'parent' => array(
      'core/list'
    ),
    'description' => '建立列表项目。',
    'textdomain' => 'default',
    'attributes' => array(
      'placeholder' => array(
        'type' => 'string'
      ),
      'content' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'li',
        'default' => '',
        '__experimentalRole' => 'content'
      )
    ),
    'supports' => array(
      'className' => false,
      '__experimentalSelector' => 'li',
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'loginout' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/loginout',
    'title' => '登录/注销',
    'category' => 'theme',
    'description' => '显示登录和注销链接。',
    'keywords' => array(
      'login',
      'logout',
      'form'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'displayLoginAsForm' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'redirectToCurrent' => array(
        'type' => 'boolean',
        'default' => true
      )
    ),
    'supports' => array(
      'className' => true,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'media-text' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/media-text',
    'title' => '媒体和文本',
    'category' => 'media',
    'description' => '将媒体和文字并排设置来丰富布局。',
    'keywords' => array(
      'image',
      'video'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'align' => array(
        'type' => 'string',
        'default' => 'none'
      ),
      'mediaAlt' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure img',
        'attribute' => 'alt',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'mediaPosition' => array(
        'type' => 'string',
        'default' => 'left'
      ),
      'mediaId' => array(
        'type' => 'number',
        '__experimentalRole' => 'content'
      ),
      'mediaUrl' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure video,figure img',
        'attribute' => 'src',
        '__experimentalRole' => 'content'
      ),
      'mediaLink' => array(
        'type' => 'string'
      ),
      'linkDestination' => array(
        'type' => 'string'
      ),
      'linkTarget' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure a',
        'attribute' => 'target'
      ),
      'href' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure a',
        'attribute' => 'href',
        '__experimentalRole' => 'content'
      ),
      'rel' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure a',
        'attribute' => 'rel'
      ),
      'linkClass' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'figure a',
        'attribute' => 'class'
      ),
      'mediaType' => array(
        'type' => 'string',
        '__experimentalRole' => 'content'
      ),
      'mediaWidth' => array(
        'type' => 'number',
        'default' => 50
      ),
      'mediaSizeSlug' => array(
        'type' => 'string'
      ),
      'isStackedOnMobile' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'verticalAlignment' => array(
        'type' => 'string'
      ),
      'imageFill' => array(
        'type' => 'boolean'
      ),
      'focalPoint' => array(
        'type' => 'object'
      ),
      'allowedBlocks' => array(
        'type' => 'array'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-media-text-editor',
    'style' => 'gc-block-media-text'
  ),
  'missing' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/missing',
    'title' => '不支持',
    'category' => 'text',
    'description' => '您的系统不支持这一区块。',
    'textdomain' => 'default',
    'attributes' => array(
      'originalName' => array(
        'type' => 'string'
      ),
      'originalUndelimitedContent' => array(
        'type' => 'string'
      ),
      'originalContent' => array(
        'type' => 'string',
        'source' => 'html'
      )
    ),
    'supports' => array(
      'className' => false,
      'customClassName' => false,
      'inserter' => false,
      'html' => false,
      'reusable' => false
    )
  ),
  'more' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/more',
    'title' => '更多',
    'category' => 'design',
    'description' => '此区块前的内容将显示在您归档页的摘要中。',
    'keywords' => array(
      '阅读更多'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'customText' => array(
        'type' => 'string'
      ),
      'noTeaser' => array(
        'type' => 'boolean',
        'default' => false
      )
    ),
    'supports' => array(
      'customClassName' => false,
      'className' => false,
      'html' => false,
      'multiple' => false
    ),
    'editorStyle' => 'gc-block-more-editor'
  ),
  'navigation' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/navigation',
    'title' => '导航',
    'category' => 'theme',
    'description' => '允许访问者在您的系统内不断浏览的区块集合。',
    'keywords' => array(
      'menu',
      'navigation',
      'links'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'ref' => array(
        'type' => 'number'
      ),
      'textColor' => array(
        'type' => 'string'
      ),
      'customTextColor' => array(
        'type' => 'string'
      ),
      'rgbTextColor' => array(
        'type' => 'string'
      ),
      'backgroundColor' => array(
        'type' => 'string'
      ),
      'customBackgroundColor' => array(
        'type' => 'string'
      ),
      'rgbBackgroundColor' => array(
        'type' => 'string'
      ),
      'showSubmenuIcon' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'openSubmenusOnClick' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'overlayMenu' => array(
        'type' => 'string',
        'default' => 'mobile'
      ),
      'icon' => array(
        'type' => 'string',
        'default' => 'handle'
      ),
      'hasIcon' => array(
        'type' => 'boolean',
        'default' => true
      ),
      '__unstableLocation' => array(
        'type' => 'string'
      ),
      'overlayBackgroundColor' => array(
        'type' => 'string'
      ),
      'customOverlayBackgroundColor' => array(
        'type' => 'string'
      ),
      'overlayTextColor' => array(
        'type' => 'string'
      ),
      'customOverlayTextColor' => array(
        'type' => 'string'
      ),
      'maxNestingLevel' => array(
        'type' => 'number',
        'default' => 5
      ),
      'templateLock' => array(
        'type' => array(
          'string',
          'boolean'
        ),
        'enum' => array(
          'all',
          'insert',
          'contentOnly',
          false
        )
      )
    ),
    'providesContext' => array(
      'textColor' => 'textColor',
      'customTextColor' => 'customTextColor',
      'backgroundColor' => 'backgroundColor',
      'customBackgroundColor' => 'customBackgroundColor',
      'overlayTextColor' => 'overlayTextColor',
      'customOverlayTextColor' => 'customOverlayTextColor',
      'overlayBackgroundColor' => 'overlayBackgroundColor',
      'customOverlayBackgroundColor' => 'customOverlayBackgroundColor',
      'fontSize' => 'fontSize',
      'customFontSize' => 'customFontSize',
      'showSubmenuIcon' => 'showSubmenuIcon',
      'openSubmenusOnClick' => 'openSubmenusOnClick',
      'style' => 'style',
      'maxNestingLevel' => 'maxNestingLevel'
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'inserter' => true,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalTextTransform' => true,
        '__experimentalFontFamily' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalSkipSerialization' => array(
          'textDecoration'
        ),
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'spacing' => array(
        'blockGap' => true,
        'units' => array(
          'px',
          'em',
          'rem',
          'vh',
          'vw'
        ),
        '__experimentalDefaultControls' => array(
          'blockGap' => true
        )
      ),
      'layout' => array(
        'allowSwitching' => false,
        'allowInheriting' => false,
        'allowVerticalAlignment' => false,
        'allowSizingOnChildren' => true,
        'default' => array(
          'type' => 'flex'
        )
      ),
      '__experimentalStyle' => array(
        'elements' => array(
          'link' => array(
            'color' => array(
              'text' => 'inherit'
            )
          )
        )
      )
    ),
    'viewScript' => array(
      'file:./view.min.js',
      'file:./view-modal.min.js'
    ),
    'editorStyle' => 'gc-block-navigation-editor',
    'style' => 'gc-block-navigation'
  ),
  'navigation-link' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/navigation-link',
    'title' => '自定义链接',
    'category' => 'design',
    'parent' => array(
      'core/navigation'
    ),
    'description' => '将页面、链接或其他项目添加到导航中。',
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string'
      ),
      'type' => array(
        'type' => 'string'
      ),
      'description' => array(
        'type' => 'string'
      ),
      'rel' => array(
        'type' => 'string'
      ),
      'id' => array(
        'type' => 'number'
      ),
      'opensInNewTab' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'url' => array(
        'type' => 'string'
      ),
      'title' => array(
        'type' => 'string'
      ),
      'kind' => array(
        'type' => 'string'
      ),
      'isTopLevelLink' => array(
        'type' => 'boolean'
      )
    ),
    'usesContext' => array(
      'textColor',
      'customTextColor',
      'backgroundColor',
      'customBackgroundColor',
      'overlayTextColor',
      'customOverlayTextColor',
      'overlayBackgroundColor',
      'customOverlayBackgroundColor',
      'fontSize',
      'customFontSize',
      'showSubmenuIcon',
      'maxNestingLevel',
      'style'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      '__experimentalSlashInserter' => true,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-navigation-link-editor',
    'style' => 'gc-block-navigation-link'
  ),
  'navigation-submenu' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/navigation-submenu',
    'title' => '子菜单',
    'category' => 'design',
    'parent' => array(
      'core/navigation'
    ),
    'description' => '将子菜单添加到您的导航中。',
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string'
      ),
      'type' => array(
        'type' => 'string'
      ),
      'description' => array(
        'type' => 'string'
      ),
      'rel' => array(
        'type' => 'string'
      ),
      'id' => array(
        'type' => 'number'
      ),
      'opensInNewTab' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'url' => array(
        'type' => 'string'
      ),
      'title' => array(
        'type' => 'string'
      ),
      'kind' => array(
        'type' => 'string'
      ),
      'isTopLevelItem' => array(
        'type' => 'boolean'
      )
    ),
    'usesContext' => array(
      'textColor',
      'customTextColor',
      'backgroundColor',
      'customBackgroundColor',
      'overlayTextColor',
      'customOverlayTextColor',
      'overlayBackgroundColor',
      'customOverlayBackgroundColor',
      'fontSize',
      'customFontSize',
      'showSubmenuIcon',
      'maxNestingLevel',
      'openSubmenusOnClick',
      'style'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false
    ),
    'editorStyle' => 'gc-block-navigation-submenu-editor',
    'style' => 'gc-block-navigation-submenu'
  ),
  'nextpage' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/nextpage',
    'title' => '分页符',
    'category' => 'design',
    'description' => '将您的内容分成多个页面。',
    'keywords' => array(
      '下一页',
      'pagination'
    ),
    'parent' => array(
      'core/post-content'
    ),
    'textdomain' => 'default',
    'supports' => array(
      'customClassName' => false,
      'className' => false,
      'html' => false
    ),
    'editorStyle' => 'gc-block-nextpage-editor'
  ),
  'page-list' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/page-list',
    'title' => '页面列表',
    'category' => 'widgets',
    'description' => '显示所有页面的列表。',
    'keywords' => array(
      'menu',
      'navigation'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'parentPageID' => array(
        'type' => 'integer',
        'default' => 0
      ),
      'isNested' => array(
        'type' => 'boolean',
        'default' => false
      )
    ),
    'usesContext' => array(
      'textColor',
      'customTextColor',
      'backgroundColor',
      'customBackgroundColor',
      'overlayTextColor',
      'customOverlayTextColor',
      'overlayBackgroundColor',
      'customOverlayBackgroundColor',
      'fontSize',
      'customFontSize',
      'showSubmenuIcon',
      'style',
      'openSubmenusOnClick'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-page-list-editor',
    'style' => 'gc-block-page-list'
  ),
  'page-list-item' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/page-list-item',
    'title' => '页面列表项',
    'category' => 'widgets',
    'parent' => array(
      'core/page-list'
    ),
    'description' => '在页面的列表中显示一个页面。',
    'keywords' => array(
      'page',
      'menu',
      'navigation'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'id' => array(
        'type' => 'number'
      ),
      'label' => array(
        'type' => 'string'
      ),
      'title' => array(
        'type' => 'string'
      ),
      'link' => array(
        'type' => 'string'
      ),
      'hasChildren' => array(
        'type' => 'boolean'
      )
    ),
    'usesContext' => array(
      'textColor',
      'customTextColor',
      'backgroundColor',
      'customBackgroundColor',
      'overlayTextColor',
      'customOverlayTextColor',
      'overlayBackgroundColor',
      'customOverlayBackgroundColor',
      'fontSize',
      'customFontSize',
      'showSubmenuIcon',
      'style',
      'openSubmenusOnClick'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'lock' => false,
      'inserter' => false,
      '__experimentalToolbar' => false
    ),
    'editorStyle' => 'gc-block-page-list-editor',
    'style' => 'gc-block-page-list'
  ),
  'paragraph' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/paragraph',
    'title' => '段落',
    'category' => 'text',
    'description' => '这是文字内容的基本要素，请以此为基础开始撰写。',
    'keywords' => array(
      'text'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'align' => array(
        'type' => 'string'
      ),
      'content' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'p',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'dropCap' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'placeholder' => array(
        'type' => 'string'
      ),
      'direction' => array(
        'type' => 'string',
        'enum' => array(
          'ltr',
          'rtl'
        )
      )
    ),
    'supports' => array(
      'anchor' => true,
      'className' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextTransform' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      '__experimentalSelector' => 'p',
      '__unstablePasteTextInline' => true
    ),
    'editorStyle' => 'gc-block-paragraph-editor',
    'style' => 'gc-block-paragraph'
  ),
  'pattern' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/pattern',
    'title' => '区块样板占位符',
    'category' => 'theme',
    'description' => '显示一个区块样板。',
    'supports' => array(
      'html' => false,
      'inserter' => false
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'slug' => array(
        'type' => 'string'
      )
    )
  ),
  'post-author' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-author',
    'title' => '文章作者',
    'category' => 'theme',
    'description' => '显示文章作者的详细信息，例如姓名、头像和个人说明。',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'avatarSize' => array(
        'type' => 'number',
        'default' => 48
      ),
      'showAvatar' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'showBio' => array(
        'type' => 'boolean'
      ),
      'byline' => array(
        'type' => 'string'
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      )
    ),
    'usesContext' => array(
      'postType',
      'postId',
      'queryId'
    ),
    'supports' => array(
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDuotone' => '.gc-block-post-author__avatar img',
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      )
    ),
    'style' => 'gc-block-post-author'
  ),
  'post-author-biography' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-author-biography',
    'title' => '文章作者简介',
    'category' => 'theme',
    'description' => '作者简介',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'postType',
      'postId'
    ),
    'supports' => array(
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'post-author-name' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-author-name',
    'title' => '文章作者名称',
    'category' => 'theme',
    'description' => '作者名称。',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      )
    ),
    'usesContext' => array(
      'postType',
      'postId'
    ),
    'supports' => array(
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'post-comments-form' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-comments-form',
    'title' => '文章评论表单',
    'category' => 'theme',
    'description' => '显示文章的评论表单。',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'postId',
      'postType'
    ),
    'supports' => array(
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextTransform' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-post-comments-form-editor',
    'style' => array(
      'gc-block-post-comments-form',
      'gc-block-buttons',
      'gc-block-button'
    )
  ),
  'post-content' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-content',
    'title' => '文章内容',
    'category' => 'theme',
    'description' => '显示文章的或页面的内容。',
    'textdomain' => 'default',
    'usesContext' => array(
      'postId',
      'postType',
      'queryId'
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'layout' => true,
      'dimensions' => array(
        'minHeight' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-post-content-editor'
  ),
  'post-date' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-date',
    'title' => '文章日期',
    'category' => 'theme',
    'description' => '添加此文章的日期。',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'format' => array(
        'type' => 'string'
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'displayType' => array(
        'type' => 'string',
        'default' => 'date'
      )
    ),
    'usesContext' => array(
      'postId',
      'postType',
      'queryId'
    ),
    'supports' => array(
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'post-excerpt' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-excerpt',
    'title' => '摘要',
    'category' => 'theme',
    'description' => '显示文章的摘要。',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'moreText' => array(
        'type' => 'string'
      ),
      'showMoreOnNewLine' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'excerptLength' => array(
        'type' => 'number',
        'default' => 55
      )
    ),
    'usesContext' => array(
      'postId',
      'postType',
      'queryId'
    ),
    'supports' => array(
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-post-excerpt-editor',
    'style' => 'gc-block-post-excerpt'
  ),
  'post-featured-image' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-featured-image',
    'title' => '文章特色图片',
    'category' => 'theme',
    'description' => '显示文章的特色图片。',
    'textdomain' => 'default',
    'attributes' => array(
      'isLink' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'aspectRatio' => array(
        'type' => 'string'
      ),
      'width' => array(
        'type' => 'string'
      ),
      'height' => array(
        'type' => 'string'
      ),
      'scale' => array(
        'type' => 'string',
        'default' => 'cover'
      ),
      'sizeSlug' => array(
        'type' => 'string'
      ),
      'rel' => array(
        'type' => 'string',
        'attribute' => 'rel',
        'default' => ''
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      ),
      'overlayColor' => array(
        'type' => 'string'
      ),
      'customOverlayColor' => array(
        'type' => 'string'
      ),
      'dimRatio' => array(
        'type' => 'number',
        'default' => 0
      ),
      'gradient' => array(
        'type' => 'string'
      ),
      'customGradient' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'postId',
      'postType',
      'queryId'
    ),
    'supports' => array(
      'align' => array(
        'left',
        'right',
        'center',
        'wide',
        'full'
      ),
      'color' => array(
        '__experimentalDuotone' => 'img, .gc-block-post-featured-image__placeholder, .components-placeholder__illustration, .components-placeholder::before',
        'text' => false,
        'background' => false
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'width' => true,
        '__experimentalSelector' => 'img, .block-editor-media-placeholder, .gc-block-post-featured-image__overlay',
        '__experimentalSkipSerialization' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'width' => true
        )
      ),
      'html' => false,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      )
    ),
    'editorStyle' => 'gc-block-post-featured-image-editor',
    'style' => 'gc-block-post-featured-image'
  ),
  'post-navigation-link' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-navigation-link',
    'title' => '文章导航链接',
    'category' => 'theme',
    'description' => '显示与当前文章相邻的下一篇或上一篇文章的链接。',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'type' => array(
        'type' => 'string',
        'default' => 'next'
      ),
      'label' => array(
        'type' => 'string'
      ),
      'showTitle' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'linkLabel' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'arrow' => array(
        'type' => 'string',
        'default' => 'none'
      )
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'color' => array(
        'link' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'style' => 'gc-block-post-navigation-link'
  ),
  'post-template' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-template',
    'title' => '文章模板',
    'category' => 'theme',
    'parent' => array(
      'core/query'
    ),
    'description' => '包含用于呈现文章的区块元素，如标题、日期、特色图片、内容或摘要等。',
    'textdomain' => 'default',
    'usesContext' => array(
      'queryId',
      'query',
      'queryContext',
      'displayLayout',
      'templateSlug',
      'previewPostType'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'align' => array(
        'wide',
        'full'
      ),
      'layout' => true,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      'spacing' => array(
        'blockGap' => array(
          '__experimentalDefault' => '1.25em'
        ),
        '__experimentalDefaultControls' => array(
          'blockGap' => true
        )
      )
    ),
    'style' => 'gc-block-post-template',
    'editorStyle' => 'gc-block-post-template-editor'
  ),
  'post-terms' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-terms',
    'title' => '文章项目',
    'category' => 'theme',
    'description' => '文章项目。',
    'textdomain' => 'default',
    'attributes' => array(
      'term' => array(
        'type' => 'string'
      ),
      'textAlign' => array(
        'type' => 'string'
      ),
      'separator' => array(
        'type' => 'string',
        'default' => ', '
      ),
      'prefix' => array(
        'type' => 'string',
        'default' => ''
      ),
      'suffix' => array(
        'type' => 'string',
        'default' => ''
      )
    ),
    'usesContext' => array(
      'postId',
      'postType'
    ),
    'supports' => array(
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'style' => 'gc-block-post-terms'
  ),
  'post-title' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/post-title',
    'title' => '标题',
    'category' => 'theme',
    'description' => '显示文章、页面或任何其他内容类型的标题。',
    'textdomain' => 'default',
    'usesContext' => array(
      'postId',
      'postType',
      'queryId'
    ),
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      ),
      'level' => array(
        'type' => 'number',
        'default' => 2
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'rel' => array(
        'type' => 'string',
        'attribute' => 'rel',
        'default' => ''
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      )
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'fontAppearance' => true,
          'textTransform' => true
        )
      )
    ),
    'style' => 'gc-block-post-title'
  ),
  'preformatted' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/preformatted',
    'title' => '预格式',
    'category' => 'text',
    'description' => '添加符合间距和标签的文字，也可设置样式。',
    'textdomain' => 'default',
    'attributes' => array(
      'content' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'pre',
        'default' => '',
        '__unstablePreserveWhiteSpace' => true,
        '__experimentalRole' => 'content'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'color' => array(
        'gradients' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'style' => 'gc-block-preformatted'
  ),
  'pullquote' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/pullquote',
    'title' => '引文',
    'category' => 'text',
    'description' => '为您文中的引用增添特殊的视觉显示效果。',
    'textdomain' => 'default',
    'attributes' => array(
      'value' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'p',
        '__experimentalRole' => 'content'
      ),
      'citation' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'cite',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => array(
        'left',
        'right',
        'wide',
        'full'
      ),
      'color' => array(
        'gradients' => true,
        'background' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'fontAppearance' => true
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'style' => true,
        'width' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'style' => true,
          'width' => true
        )
      ),
      '__experimentalStyle' => array(
        'typography' => array(
          'fontSize' => '1.5em',
          'lineHeight' => '1.6'
        )
      )
    ),
    'editorStyle' => 'gc-block-pullquote-editor',
    'style' => 'gc-block-pullquote'
  ),
  'query' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/query',
    'title' => '循环查询',
    'category' => 'theme',
    'description' => '一个可以根据不同的查询参数和视觉配置来显示文章类型的高级区块。',
    'textdomain' => 'default',
    'attributes' => array(
      'queryId' => array(
        'type' => 'number'
      ),
      'query' => array(
        'type' => 'object',
        'default' => array(
          'perPage' => null,
          'pages' => 0,
          'offset' => 0,
          'postType' => 'post',
          'order' => 'desc',
          'orderBy' => 'date',
          'author' => '',
          'search' => '',
          'exclude' => array(
            
          ),
          'sticky' => '',
          'inherit' => true,
          'taxQuery' => null,
          'parents' => array(
            
          )
        )
      ),
      'tagName' => array(
        'type' => 'string',
        'default' => 'div'
      ),
      'namespace' => array(
        'type' => 'string'
      )
    ),
    'providesContext' => array(
      'queryId' => 'queryId',
      'query' => 'query',
      'displayLayout' => 'displayLayout'
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'layout' => true
    ),
    'editorStyle' => 'gc-block-query-editor'
  ),
  'query-no-results' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/query-no-results',
    'title' => '无结果',
    'category' => 'theme',
    'description' => '包含在没有找到查询结果的时候用于渲染内容的区块元素。',
    'parent' => array(
      'core/query'
    ),
    'textdomain' => 'default',
    'usesContext' => array(
      'queryId',
      'query'
    ),
    'supports' => array(
      'align' => true,
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'query-pagination' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/query-pagination',
    'title' => '分页',
    'category' => 'theme',
    'parent' => array(
      'core/query'
    ),
    'description' => '如果适用，显示下一组/上一组文章的分页导航。',
    'textdomain' => 'default',
    'attributes' => array(
      'paginationArrow' => array(
        'type' => 'string',
        'default' => 'none'
      ),
      'showLabel' => array(
        'type' => 'boolean',
        'default' => true
      )
    ),
    'usesContext' => array(
      'queryId',
      'query'
    ),
    'providesContext' => array(
      'paginationArrow' => 'paginationArrow',
      'showLabel' => 'showLabel'
    ),
    'supports' => array(
      'align' => true,
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'layout' => array(
        'allowSwitching' => false,
        'allowInheriting' => false,
        'default' => array(
          'type' => 'flex'
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-query-pagination-editor',
    'style' => 'gc-block-query-pagination'
  ),
  'query-pagination-next' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/query-pagination-next',
    'title' => '下一页',
    'category' => 'theme',
    'parent' => array(
      'core/query-pagination'
    ),
    'description' => '显示“下一篇文章”的页面链接。',
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'queryId',
      'query',
      'paginationArrow',
      'showLabel'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'query-pagination-numbers' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/query-pagination-numbers',
    'title' => '页码',
    'category' => 'theme',
    'parent' => array(
      'core/query-pagination'
    ),
    'description' => '显示分页的页码列表',
    'textdomain' => 'default',
    'usesContext' => array(
      'queryId',
      'query'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-query-pagination-numbers-editor'
  ),
  'query-pagination-previous' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/query-pagination-previous',
    'title' => '上一页',
    'category' => 'theme',
    'parent' => array(
      'core/query-pagination'
    ),
    'description' => '显示“上一篇文章”的页面链接。',
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'queryId',
      'query',
      'paginationArrow',
      'showLabel'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false,
      'color' => array(
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'query-title' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/query-title',
    'title' => '查询标题',
    'category' => 'theme',
    'description' => '显示查询标题。',
    'textdomain' => 'default',
    'attributes' => array(
      'type' => array(
        'type' => 'string'
      ),
      'textAlign' => array(
        'type' => 'string'
      ),
      'level' => array(
        'type' => 'number',
        'default' => 1
      ),
      'showPrefix' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'showSearchTerm' => array(
        'type' => 'boolean',
        'default' => true
      )
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'gradients' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'fontAppearance' => true,
          'textTransform' => true
        )
      )
    ),
    'style' => 'gc-block-query-title'
  ),
  'quote' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/quote',
    'title' => '引用',
    'category' => 'text',
    'description' => '给引文提供视觉强调。“在引用其他人时我们也加强了自己的论述。”——胡里奥·科塔萨尔',
    'keywords' => array(
      'blockquote',
      'cite'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'value' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'blockquote',
        'multiline' => 'p',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'citation' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'cite',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'align' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'html' => false,
      '__experimentalOnEnter' => true,
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'fontAppearance' => true
        )
      ),
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      )
    ),
    'styles' => array(
      array(
        'name' => 'default',
        'label' => 'Default',
        'isDefault' => true
      ),
      array(
        'name' => 'plain',
        'label' => 'Plain'
      )
    ),
    'editorStyle' => 'gc-block-quote-editor',
    'style' => 'gc-block-quote'
  ),
  'read-more' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/read-more',
    'title' => '阅读更多',
    'category' => 'theme',
    'description' => '显示文章、页面或任何其他内容类型的链接。',
    'textdomain' => 'default',
    'attributes' => array(
      'content' => array(
        'type' => 'string'
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      )
    ),
    'usesContext' => array(
      'postId'
    ),
    'supports' => array(
      'html' => false,
      'color' => array(
        'gradients' => true,
        'text' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'textDecoration' => true
        )
      ),
      'spacing' => array(
        'margin' => array(
          'top',
          'bottom'
        ),
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'padding' => true
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'width' => true,
        '__experimentalDefaultControls' => array(
          'width' => true
        )
      )
    ),
    'style' => 'gc-block-read-more'
  ),
  'rss' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/rss',
    'title' => 'RSS',
    'category' => 'widgets',
    'description' => '显示来自任何RSS或Atom Feed的条目。',
    'keywords' => array(
      'atom',
      'feed'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'columns' => array(
        'type' => 'number',
        'default' => 2
      ),
      'blockLayout' => array(
        'type' => 'string',
        'default' => 'list'
      ),
      'feedURL' => array(
        'type' => 'string',
        'default' => ''
      ),
      'itemsToShow' => array(
        'type' => 'number',
        'default' => 5
      ),
      'displayExcerpt' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'displayAuthor' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'displayDate' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'excerptLength' => array(
        'type' => 'number',
        'default' => 55
      )
    ),
    'supports' => array(
      'align' => true,
      'html' => false
    ),
    'editorStyle' => 'gc-block-rss-editor',
    'style' => 'gc-block-rss'
  ),
  'search' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/search',
    'title' => '搜索',
    'category' => 'widgets',
    'description' => '帮助访客找到您的内容。',
    'keywords' => array(
      'find'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'label' => array(
        'type' => 'string',
        '__experimentalRole' => 'content'
      ),
      'showLabel' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'placeholder' => array(
        'type' => 'string',
        'default' => '',
        '__experimentalRole' => 'content'
      ),
      'width' => array(
        'type' => 'number'
      ),
      'widthUnit' => array(
        'type' => 'string'
      ),
      'buttonText' => array(
        'type' => 'string',
        '__experimentalRole' => 'content'
      ),
      'buttonPosition' => array(
        'type' => 'string',
        'default' => 'button-outside'
      ),
      'buttonUseIcon' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'query' => array(
        'type' => 'object',
        'default' => array(
          
        )
      ),
      'buttonBehavior' => array(
        'type' => 'string',
        'default' => 'expand-searchfield'
      ),
      'isSearchFieldHidden' => array(
        'type' => 'boolean',
        'default' => false
      )
    ),
    'supports' => array(
      'align' => array(
        'left',
        'center',
        'right'
      ),
      'color' => array(
        'gradients' => true,
        '__experimentalSkipSerialization' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        '__experimentalSkipSerialization' => true,
        '__experimentalSelector' => '.gc-block-search__label, .gc-block-search__input, .gc-block-search__button',
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      '__experimentalBorder' => array(
        'color' => true,
        'radius' => true,
        'width' => true,
        '__experimentalSkipSerialization' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'radius' => true,
          'width' => true
        )
      ),
      'html' => false
    ),
    'viewScript' => 'file:./view.min.js',
    'editorStyle' => 'gc-block-search-editor',
    'style' => 'gc-block-search'
  ),
  'separator' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/separator',
    'title' => '分隔符',
    'category' => 'design',
    'description' => '用水平分隔符在点子或章节之间创造分隔符。',
    'keywords' => array(
      'horizontal-line',
      'hr',
      'divider'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'opacity' => array(
        'type' => 'string',
        'default' => 'alpha-channel'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => array(
        'center',
        'wide',
        'full'
      ),
      'color' => array(
        'enableContrastChecker' => false,
        '__experimentalSkipSerialization' => true,
        'gradients' => true,
        'background' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => true
        )
      ),
      'spacing' => array(
        'margin' => array(
          'top',
          'bottom'
        )
      )
    ),
    'styles' => array(
      array(
        'name' => 'default',
        'label' => 'Default',
        'isDefault' => true
      ),
      array(
        'name' => 'wide',
        'label' => '宽线'
      ),
      array(
        'name' => 'dots',
        'label' => 'Dots'
      )
    ),
    'editorStyle' => 'gc-block-separator-editor',
    'style' => 'gc-block-separator'
  ),
  'shortcode' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/shortcode',
    'title' => '简码',
    'category' => 'widgets',
    'description' => '通过 GeChiUI 简码插入额外的自定义元素。',
    'textdomain' => 'default',
    'attributes' => array(
      'text' => array(
        'type' => 'string',
        'source' => 'raw'
      )
    ),
    'supports' => array(
      'className' => false,
      'customClassName' => false,
      'html' => false
    ),
    'editorStyle' => 'gc-block-shortcode-editor'
  ),
  'site-logo' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/site-logo',
    'title' => '系统 Logo ',
    'category' => 'theme',
    'description' => '显示代表该系统的图像。更新此区块后，更改将应用到所有地方。',
    'textdomain' => 'default',
    'attributes' => array(
      'width' => array(
        'type' => 'number'
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      ),
      'shouldSyncIcon' => array(
        'type' => 'boolean'
      )
    ),
    'example' => array(
      'viewportWidth' => 500,
      'attributes' => array(
        'width' => 350,
        'className' => 'block-editor-block-types-list__site-logo-example'
      )
    ),
    'supports' => array(
      'html' => false,
      'align' => true,
      'alignWide' => false,
      'color' => array(
        '__experimentalDuotone' => 'img, .components-placeholder__illustration, .components-placeholder::before',
        'text' => false,
        'background' => false
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      )
    ),
    'styles' => array(
      array(
        'name' => 'default',
        'label' => 'Default',
        'isDefault' => true
      ),
      array(
        'name' => 'rounded',
        'label' => 'Rounded'
      )
    ),
    'editorStyle' => 'gc-block-site-logo-editor',
    'style' => 'gc-block-site-logo'
  ),
  'site-tagline' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/site-tagline',
    'title' => '系统副标题',
    'category' => 'theme',
    'description' => '用几句话描述此系统的内容。副标题可以出现在搜索结果中或在社交网络上分享时使用，即使它没有显示在主题设计中。',
    'keywords' => array(
      'description'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'example' => array(
      
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'gradients' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-site-tagline-editor'
  ),
  'site-title' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/site-title',
    'title' => '系统标题',
    'category' => 'theme',
    'description' => '显示此系统的名称。更新该区块后，其更改将显示在所有应用该区块的位置。也会显示在浏览器标题栏和搜索结果中。',
    'textdomain' => 'default',
    'attributes' => array(
      'level' => array(
        'type' => 'number',
        'default' => 1
      ),
      'textAlign' => array(
        'type' => 'string'
      ),
      'isLink' => array(
        'type' => 'boolean',
        'default' => true
      ),
      'linkTarget' => array(
        'type' => 'string',
        'default' => '_self'
      )
    ),
    'example' => array(
      'viewportWidth' => 500
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true,
          'link' => true
        )
      ),
      'spacing' => array(
        'padding' => true,
        'margin' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'lineHeight' => true,
          'fontAppearance' => true,
          'letterSpacing' => true,
          'textTransform' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-site-title-editor',
    'style' => 'gc-block-site-title'
  ),
  'social-link' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/social-link',
    'title' => '社交图标',
    'category' => 'widgets',
    'parent' => array(
      'core/social-links'
    ),
    'description' => '显示链接至您的社交媒体资料或系统的图标。',
    'textdomain' => 'default',
    'attributes' => array(
      'url' => array(
        'type' => 'string'
      ),
      'service' => array(
        'type' => 'string'
      ),
      'label' => array(
        'type' => 'string'
      ),
      'rel' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'openInNewTab',
      'showLabels',
      'iconColor',
      'iconColorValue',
      'iconBackgroundColor',
      'iconBackgroundColorValue'
    ),
    'supports' => array(
      'reusable' => false,
      'html' => false
    ),
    'editorStyle' => 'gc-block-social-link-editor'
  ),
  'social-links' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/social-links',
    'title' => '社交图标',
    'category' => 'widgets',
    'description' => '显示链接到您的社交媒体资料或系统的图标。',
    'keywords' => array(
      'links'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'iconColor' => array(
        'type' => 'string'
      ),
      'customIconColor' => array(
        'type' => 'string'
      ),
      'iconColorValue' => array(
        'type' => 'string'
      ),
      'iconBackgroundColor' => array(
        'type' => 'string'
      ),
      'customIconBackgroundColor' => array(
        'type' => 'string'
      ),
      'iconBackgroundColorValue' => array(
        'type' => 'string'
      ),
      'openInNewTab' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'showLabels' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'size' => array(
        'type' => 'string'
      )
    ),
    'providesContext' => array(
      'openInNewTab' => 'openInNewTab',
      'showLabels' => 'showLabels',
      'iconColor' => 'iconColor',
      'iconColorValue' => 'iconColorValue',
      'iconBackgroundColor' => 'iconBackgroundColor',
      'iconBackgroundColorValue' => 'iconBackgroundColorValue'
    ),
    'supports' => array(
      'align' => array(
        'left',
        'center',
        'right'
      ),
      'anchor' => true,
      '__experimentalExposeControlsToChildren' => true,
      'layout' => array(
        'allowSwitching' => false,
        'allowInheriting' => false,
        'allowVerticalAlignment' => false,
        'default' => array(
          'type' => 'flex'
        )
      ),
      'color' => array(
        'enableContrastChecker' => false,
        'background' => true,
        'gradients' => true,
        'text' => false,
        '__experimentalDefaultControls' => array(
          'background' => false
        )
      ),
      'spacing' => array(
        'blockGap' => array(
          'horizontal',
          'vertical'
        ),
        'margin' => true,
        'padding' => true,
        'units' => array(
          'px',
          'em',
          'rem',
          'vh',
          'vw'
        ),
        '__experimentalDefaultControls' => array(
          'blockGap' => true,
          'margin' => true,
          'padding' => false
        )
      )
    ),
    'styles' => array(
      array(
        'name' => 'default',
        'label' => 'Default',
        'isDefault' => true
      ),
      array(
        'name' => 'logos-only',
        'label' => '仅 Logo'
      ),
      array(
        'name' => 'pill-shape',
        'label' => '药丸形状'
      )
    ),
    'editorStyle' => 'gc-block-social-links-editor',
    'style' => 'gc-block-social-links'
  ),
  'spacer' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/spacer',
    'title' => '空格',
    'category' => 'design',
    'description' => '在区块间添加空白并自定义其高度。',
    'textdomain' => 'default',
    'attributes' => array(
      'height' => array(
        'type' => 'string',
        'default' => '100px'
      ),
      'width' => array(
        'type' => 'string'
      )
    ),
    'usesContext' => array(
      'orientation'
    ),
    'supports' => array(
      'anchor' => true,
      'spacing' => array(
        'margin' => array(
          'top',
          'bottom'
        ),
        '__experimentalDefaultControls' => array(
          'margin' => true
        )
      )
    ),
    'editorStyle' => 'gc-block-spacer-editor',
    'style' => 'gc-block-spacer'
  ),
  'table' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/table',
    'title' => '表格',
    'category' => 'text',
    'description' => '在行和列中创建结构化内容以显示信息。',
    'textdomain' => 'default',
    'attributes' => array(
      'hasFixedLayout' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'caption' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'figcaption',
        'default' => ''
      ),
      'head' => array(
        'type' => 'array',
        'default' => array(
          
        ),
        'source' => 'query',
        'selector' => 'thead tr',
        'query' => array(
          'cells' => array(
            'type' => 'array',
            'default' => array(
              
            ),
            'source' => 'query',
            'selector' => 'td,th',
            'query' => array(
              'content' => array(
                'type' => 'string',
                'source' => 'html'
              ),
              'tag' => array(
                'type' => 'string',
                'default' => 'td',
                'source' => 'tag'
              ),
              'scope' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'scope'
              ),
              'align' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'data-align'
              ),
              'colspan' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'colspan'
              ),
              'rowspan' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'rowspan'
              )
            )
          )
        )
      ),
      'body' => array(
        'type' => 'array',
        'default' => array(
          
        ),
        'source' => 'query',
        'selector' => 'tbody tr',
        'query' => array(
          'cells' => array(
            'type' => 'array',
            'default' => array(
              
            ),
            'source' => 'query',
            'selector' => 'td,th',
            'query' => array(
              'content' => array(
                'type' => 'string',
                'source' => 'html'
              ),
              'tag' => array(
                'type' => 'string',
                'default' => 'td',
                'source' => 'tag'
              ),
              'scope' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'scope'
              ),
              'align' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'data-align'
              ),
              'colspan' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'colspan'
              ),
              'rowspan' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'rowspan'
              )
            )
          )
        )
      ),
      'foot' => array(
        'type' => 'array',
        'default' => array(
          
        ),
        'source' => 'query',
        'selector' => 'tfoot tr',
        'query' => array(
          'cells' => array(
            'type' => 'array',
            'default' => array(
              
            ),
            'source' => 'query',
            'selector' => 'td,th',
            'query' => array(
              'content' => array(
                'type' => 'string',
                'source' => 'html'
              ),
              'tag' => array(
                'type' => 'string',
                'default' => 'td',
                'source' => 'tag'
              ),
              'scope' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'scope'
              ),
              'align' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'data-align'
              ),
              'colspan' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'colspan'
              ),
              'rowspan' => array(
                'type' => 'string',
                'source' => 'attribute',
                'attribute' => 'rowspan'
              )
            )
          )
        )
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => true,
      'color' => array(
        '__experimentalSkipSerialization' => true,
        'gradients' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      ),
      '__experimentalBorder' => array(
        '__experimentalSkipSerialization' => true,
        'color' => true,
        'style' => true,
        'width' => true,
        '__experimentalDefaultControls' => array(
          'color' => true,
          'style' => true,
          'width' => true
        )
      ),
      '__experimentalSelector' => '.gc-block-table > table'
    ),
    'styles' => array(
      array(
        'name' => 'regular',
        'label' => 'Default',
        'isDefault' => true
      ),
      array(
        'name' => 'stripes',
        'label' => 'Stripes'
      )
    ),
    'editorStyle' => 'gc-block-table-editor',
    'style' => 'gc-block-table'
  ),
  'tag-cloud' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/tag-cloud',
    'title' => '标签云',
    'category' => 'widgets',
    'description' => '您最常使用的标签云。',
    'textdomain' => 'default',
    'attributes' => array(
      'numberOfTags' => array(
        'type' => 'number',
        'default' => 45,
        'minimum' => 1,
        'maximum' => 100
      ),
      'taxonomy' => array(
        'type' => 'string',
        'default' => 'post_tag'
      ),
      'showTagCounts' => array(
        'type' => 'boolean',
        'default' => false
      ),
      'smallestFontSize' => array(
        'type' => 'string',
        'default' => '8pt'
      ),
      'largestFontSize' => array(
        'type' => 'string',
        'default' => '22pt'
      )
    ),
    'styles' => array(
      array(
        'name' => 'default',
        'label' => 'Default',
        'isDefault' => true
      ),
      array(
        'name' => 'outline',
        'label' => 'Outline'
      )
    ),
    'supports' => array(
      'html' => false,
      'align' => true,
      'spacing' => array(
        'margin' => true,
        'padding' => true
      ),
      'typography' => array(
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalLetterSpacing' => true
      )
    ),
    'editorStyle' => 'gc-block-tag-cloud-editor'
  ),
  'template-part' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/template-part',
    'title' => '模版组件',
    'category' => 'theme',
    'description' => '编辑系统的不同全局区域，如页眉、页脚、侧边栏，或创建自己的区域。',
    'textdomain' => 'default',
    'attributes' => array(
      'slug' => array(
        'type' => 'string'
      ),
      'theme' => array(
        'type' => 'string'
      ),
      'tagName' => array(
        'type' => 'string'
      ),
      'area' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'align' => true,
      'html' => false,
      'reusable' => false
    ),
    'editorStyle' => 'gc-block-template-part-editor'
  ),
  'term-description' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/term-description',
    'title' => '项目描述',
    'category' => 'theme',
    'description' => '查看归档时显示分类、标签和自定义分类法的描述。',
    'textdomain' => 'default',
    'attributes' => array(
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'align' => array(
        'wide',
        'full'
      ),
      'html' => false,
      'color' => array(
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'spacing' => array(
        'padding' => true,
        'margin' => true
      ),
      'typography' => array(
        'fontSize' => true,
        'lineHeight' => true,
        '__experimentalFontFamily' => true,
        '__experimentalFontWeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true
        )
      )
    )
  ),
  'text-columns' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/text-columns',
    'title' => '文本栏（已弃用）',
    'icon' => 'columns',
    'category' => 'design',
    'description' => '此区块已弃用。请改用“栏目”区块。',
    'textdomain' => 'default',
    'attributes' => array(
      'content' => array(
        'type' => 'array',
        'source' => 'query',
        'selector' => 'p',
        'query' => array(
          'children' => array(
            'type' => 'string',
            'source' => 'html'
          )
        ),
        'default' => array(
          array(
            
          ),
          array(
            
          )
        )
      ),
      'columns' => array(
        'type' => 'number',
        'default' => 2
      ),
      'width' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'inserter' => false
    ),
    'editorStyle' => 'gc-block-text-columns-editor',
    'style' => 'gc-block-text-columns'
  ),
  'verse' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/verse',
    'title' => '诗篇',
    'category' => 'text',
    'description' => '插入诗歌，使用特殊的空白格式，或引用歌词。',
    'keywords' => array(
      'poetry',
      'poem'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'content' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'pre',
        'default' => '',
        '__unstablePreserveWhiteSpace' => true,
        '__experimentalRole' => 'content'
      ),
      'textAlign' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'anchor' => true,
      'color' => array(
        'gradients' => true,
        'link' => true,
        '__experimentalDefaultControls' => array(
          'background' => true,
          'text' => true
        )
      ),
      'typography' => array(
        'fontSize' => true,
        '__experimentalFontFamily' => true,
        'lineHeight' => true,
        '__experimentalFontStyle' => true,
        '__experimentalFontWeight' => true,
        '__experimentalLetterSpacing' => true,
        '__experimentalTextTransform' => true,
        '__experimentalTextDecoration' => true,
        '__experimentalDefaultControls' => array(
          'fontSize' => true,
          'fontAppearance' => true
        )
      ),
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      ),
      '__experimentalBorder' => array(
        'radius' => true,
        'width' => true,
        'color' => true,
        'style' => true
      )
    ),
    'style' => 'gc-block-verse',
    'editorStyle' => 'gc-block-verse-editor'
  ),
  'video' => array(
    '$schema' => 'https://schemas.gc.org/trunk/block.json',
    'apiVersion' => 3,
    'name' => 'core/video',
    'title' => '视频',
    'category' => 'media',
    'description' => '嵌入您媒体库中的视频或上传新的视频。',
    'keywords' => array(
      'movie'
    ),
    'textdomain' => 'default',
    'attributes' => array(
      'autoplay' => array(
        'type' => 'boolean',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'autoplay'
      ),
      'caption' => array(
        'type' => 'string',
        'source' => 'html',
        'selector' => 'figcaption',
        '__experimentalRole' => 'content'
      ),
      'controls' => array(
        'type' => 'boolean',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'controls',
        'default' => true
      ),
      'id' => array(
        'type' => 'number',
        '__experimentalRole' => 'content'
      ),
      'loop' => array(
        'type' => 'boolean',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'loop'
      ),
      'muted' => array(
        'type' => 'boolean',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'muted'
      ),
      'poster' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'poster'
      ),
      'preload' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'preload',
        'default' => 'metadata'
      ),
      'src' => array(
        'type' => 'string',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'src',
        '__experimentalRole' => 'content'
      ),
      'playsInline' => array(
        'type' => 'boolean',
        'source' => 'attribute',
        'selector' => 'video',
        'attribute' => 'playsinline'
      ),
      'tracks' => array(
        '__experimentalRole' => 'content',
        'type' => 'array',
        'items' => array(
          'type' => 'object'
        ),
        'default' => array(
          
        )
      )
    ),
    'supports' => array(
      'anchor' => true,
      'align' => true,
      'spacing' => array(
        'margin' => true,
        'padding' => true,
        '__experimentalDefaultControls' => array(
          'margin' => false,
          'padding' => false
        )
      )
    ),
    'editorStyle' => 'gc-block-video-editor',
    'style' => 'gc-block-video'
  ),
  'widget-group' => array(
    'apiVersion' => 3,
    'name' => 'core/widget-group',
    'category' => 'widgets',
    'attributes' => array(
      'title' => array(
        'type' => 'string'
      )
    ),
    'supports' => array(
      'html' => false,
      'inserter' => true,
      'customClassName' => true,
      'reusable' => false
    ),
    'editorStyle' => 'gc-block-widget-group-editor',
    'style' => 'gc-block-widget-group'
  )
);