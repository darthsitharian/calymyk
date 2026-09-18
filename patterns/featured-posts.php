<?php
/**
 * Title: Featured Posts
 * Slug: calymyk-new/featured-posts
 * Inserter: false
 */
?>

<!-- wp:group {"className":"cm-container-wide cm-featured-posts","layout":{"type":"default"}} -->
<div class="wp-block-group cm-container-wide cm-featured-posts">

	<!-- wp:group {"className":"cm-section-header cm-grid","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-section-header cm-grid">

		<!-- wp:group {"className":"cm-span-8","layout":{"type":"default"}} -->
		<div class="wp-block-group cm-span-8">

			<!-- wp:paragraph {"className":"cm-eyebrow"} -->
			<p class="cm-eyebrow">NAJNOWSZE</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">Warto przeczytać.</h2>
			<!-- /wp:heading -->

		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"cm-span-4 cm-section-header__aside","layout":{"type":"default"}} -->
		<div class="wp-block-group cm-span-4 cm-section-header__aside">

			<!-- wp:paragraph -->
			<p>Wybrane materiały, pomysły i wiedza do wykorzystania w praktyce.</p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:group -->

	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"40px"} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"grid","columns":12},"className":"cm-featured-query"} -->
	<div class="wp-block-query cm-featured-query">

		<!-- wp:post-template {"className":"cm-grid cm-featured-posts-grid","layout":{"type":"default"}} -->

			<!-- wp:group {"className":"cm-featured-post-card","layout":{"type":"default"}} -->
			<div class="wp-block-group cm-featured-post-card">

				<!-- wp:post-featured-image {"isLink":true,"height":"360px","className":"cm-featured-post-card__image"} /-->

				<!-- wp:post-terms {"term":"category","className":"cm-featured-post-card__category"} /-->

				<!-- wp:post-title {"isLink":true,"level":3,"className":"cm-featured-post-card__title"} /-->

				<!-- wp:post-excerpt {"moreText":"","excerptLength":22,"className":"cm-featured-post-card__excerpt"} /-->

				<!-- wp:read-more {"content":"Czytaj dalej →","className":"cm-featured-post-card__link"} /-->

			</div>
			<!-- /wp:group -->

		<!-- /wp:post-template -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->
