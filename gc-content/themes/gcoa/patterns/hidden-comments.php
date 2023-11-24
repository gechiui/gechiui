<?php
/**
 * Title: Hidden Comments
 * Slug: gcoa/hidden-comments
 * Inserter: no
 */
?>


<!-- gc:comments -->
<div class="gc-block-comments">
<!-- gc:separator -->
<hr class="gc-block-separator"/>
<!-- /gc:separator -->

<!-- gc:comments-title {"level":5,"showPostTitle":false} /-->


<!-- gc:group {"className":"m-t-20","layout":{"inherit":true}} -->
<div class="gc-block-group m-t-20">
	<!-- gc:list {"className":"list-group list-group-flush"} -->
	<ul class="list-group list-group-flush">
		<!-- gc:comment-template -->
		<!-- gc:list-item {"className":"list-group-item p-h-0"} -->
		<li class="list-group-item p-h-0">
			<!-- gc:group {"className":"media m-b-15","layout":{"inherit":true}} -->
	        <div class="gc-block-group media m-b-15">
	            <!-- gc:avatar {"className":"m-r-10","size":40,"style":{"border":{"radius":"20px"}}} /-->
	            <!-- gc:group {"className":"media-body m-l-20","layout":{"inherit":true}} -->
	            <div class="gc-block-group media-body">
	            	<!-- gc:heading {"level":6,"className":"m-b-0"} -->
	                <h6 class="m-b-0"><!-- gc:comment-author-name /--></h6>
	                <!-- /gc:heading -->
	                <!-- gc:comment-date {"format":"Y年n月j日 H:i:s"} /-->
	            </div>
	            <!-- /gc:group -->
	        </div>
	        <!-- /gc:group -->
	        <!-- gc:comment-content /-->
	        <!-- gc:group {"className":"m-t-15","layout":{"inherit":true}} -->
	        <div class="gc-block-group m-t-15">
	        	<!-- gc:list {"className":"list-inline text-right"} -->
	            <ul class="list-inline text-right">
	            	<!-- gc:list-item {"className":"d-inline-block m-r-20"} -->
	            	<li class="d-inline-block m-r-20"><!-- gc:comment-reply-link /--></li>
	            	<!-- /gc:list-item -->
	            	<!-- gc:list-item {"className":"d-inline-block m-r-20"} -->
	            	<li class="d-inline-block m-r-20"><!-- gc:comment-edit-link /--></li>
	            	<!-- /gc:list-item -->
	            </ul>
	            <!-- /gc:list -->
	        </div>
	        <!-- /gc:group -->
	    </li>
	    <!-- /gc:list-item -->
		<!-- /gc:comment-template -->
	</ul>
	<!-- /gc:list -->
	<!-- gc:comments-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- gc:comments-pagination-previous /-->
		<!-- gc:comments-pagination-numbers /-->
		<!-- gc:comments-pagination-next /-->
	<!-- /gc:comments-pagination -->

	<!-- gc:post-comments-form /-->

	
</div>
<!-- /gc:group -->
</div>
<!-- /gc:comments -->
