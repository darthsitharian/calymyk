<?php
/**
 * Title: Featured Posts
 * Slug: calymyk-new/featured-posts
 * Inserter: false
 */
?>

<!-- wp:group {"className":"cm-featured-hero","layout":{"type":"default"}} -->
<div class="wp-block-group cm-featured-hero">

	<div class="wp-block-group cm-featured-hero__grid">

		<!-- LEFT: lead post + two secondary posts -->
		<div class="wp-block-group cm-featured-hero__left">

			<!-- wp:query {"queryId":10,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"cm-featured-hero__left-query"} -->
			<div class="wp-block-query cm-featured-hero__left-query">

				<!-- wp:post-template {"className":"cm-featured-hero__left-grid","layout":{"type":"default"}} -->

					<!-- wp:group {"className":"cm-featured-post-card","layout":{"type":"default"}} -->
					<div class="wp-block-group cm-featured-post-card">

						<!-- wp:post-featured-image {"isLink":false,"aspectRatio":"16/9","className":"cm-featured-post-card__image"} /-->

						<!-- wp:group {"className":"cm-featured-post-card__body","layout":{"type":"default"}} -->
						<div class="wp-block-group cm-featured-post-card__body">

							<!-- wp:calymyk/category-icon /-->
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

		<!-- RIGHT: four compact posts in one column -->
		<div class="wp-block-group cm-featured-hero__right">

			<!-- wp:query {"queryId":11,"query":{"perPage":4,"pages":0,"offset":3,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"cm-featured-hero__right-query"} -->
			<div class="wp-block-query cm-featured-hero__right-query">

				<!-- wp:post-template {"className":"cm-featured-hero__right-grid","layout":{"type":"default"}} -->

					<!-- wp:group {"className":"cm-featured-post-card cm-featured-post-card--compact","layout":{"type":"default"}} -->
					<div class="wp-block-group cm-featured-post-card cm-featured-post-card--compact">

						<!-- wp:post-featured-image {"isLink":false,"aspectRatio":"1/1","className":"cm-featured-post-card__image"} /-->

						<!-- wp:group {"className":"cm-featured-post-card__body","layout":{"type":"default"}} -->
						<div class="wp-block-group cm-featured-post-card__body">

							<!-- wp:calymyk/category-icon /-->
							<!-- wp:post-title {"isLink":true,"level":3,"className":"cm-featured-post-card__title"} /-->
							<!-- wp:post-excerpt {"moreText":"","excerptLength":18,"className":"cm-featured-post-card__excerpt"} /-->

						</div>
						<!-- /wp:group -->

					</div>
					<!-- /wp:group -->

				<!-- /wp:post-template -->

			</div>
			<!-- /wp:query -->

		</div>

	</div>
</div>
<!-- /wp:group -->
