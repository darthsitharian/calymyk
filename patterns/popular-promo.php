<?php
/**
 * Title: Popular Promo
 * Slug: calymyk-new/popular-promo
 * Inserter: false
 */
?>

<!-- wp:group {"className":"cm-popular-promo","layout":{"type":"default"}} -->
<div class="wp-block-group cm-popular-promo">
	<!-- wp:query {"queryId":30,"query":{"perPage":4,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false,"exclude":[],"sticky":""},"className":"cm-popular-promo__query"} -->
	<div class="wp-block-query cm-popular-promo__query">
		<!-- wp:post-template {"className":"cm-popular-promo__grid","layout":{"type":"default"}} -->
			<!-- wp:group {"className":"cm-popular-promo__card","layout":{"type":"default"}} -->
			<article class="wp-block-group cm-popular-promo__card">
				<!-- wp:post-featured-image {"isLink":false,"className":"cm-popular-promo__image"} /-->
				<!-- wp:group {"className":"cm-popular-promo__content","layout":{"type":"default"}} -->
				<div class="wp-block-group cm-popular-promo__content">
					<!-- wp:post-title {"isLink":true,"level":3,"className":"cm-popular-promo__title"} /-->
				</div>
				<!-- /wp:group -->
			</article>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
