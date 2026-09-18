<?php
/**
 * Title: Featured Posts
 * Slug: calymyk-new/featured-posts
 * Inserter: false
 */
?>

<!-- wp:group {"className":"cm-featured-posts","layout":{"type":"default"}} -->
<div class="wp-block-group cm-featured-posts">

	<!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"grid","columns":12},"className":"cm-featured-query"} -->
	<div class="wp-block-query cm-featured-query">

		<!-- wp:post-template {"className":"cm-grid cm-featured-posts-grid","layout":{"type":"default"}} -->

			<!-- wp:group {"className":"cm-featured-post-card","layout":{"type":"default"}} -->
			<div class="wp-block-group cm-featured-post-card">

				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","className":"cm-featured-post-card__image"} /-->

				<!-- wp:group {"className":"cm-featured-post-card__body","layout":{"type":"default"}} -->
				<div class="wp-block-group cm-featured-post-card__body">

					<!-- wp:post-terms {"term":"category","className":"cm-featured-post-card__category"} /-->

					<!-- wp:post-title {"isLink":true,"level":3,"className":"cm-featured-post-card__title"} /-->

					<!-- wp:post-excerpt {"moreText":"","excerptLength":22,"className":"cm-featured-post-card__excerpt"} /-->

					<!-- wp:read-more {"content":"Czytaj dalej →","className":"cm-featured-post-card__link"} /-->

				</div>
				<!-- /wp:group -->

			</div>
			<!-- /wp:group -->

		<!-- /wp:post-template -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->
