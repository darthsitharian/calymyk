<?php
/**
 * Title: Zyskomat Poleca
 * Slug: calymyk-new/zyskomat-poleca
 * Categories: featured, call-to-action
 * Inserter: false
 */
?>

<!-- wp:group {"className":"cm-recommendations","layout":{"type":"default"}} -->
<div class="wp-block-group cm-recommendations">

	<!-- wp:group {"className":"cm-recommendations__header","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-recommendations__header">

		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">Zyskomat poleca</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Wybrane produkty, usługi i narzędzia, które mogą ułatwić pracę albo po prostu dobrze robią swoją robotę.</p>
		<!-- /wp:paragraph -->

	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"40px"} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:query {"namespace":"calymyk-new/recommendations","queryId":20,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"cm-recommendations__query"} -->
	<div class="wp-block-query cm-recommendations__query">

		<!-- wp:post-template {"className":"cm-recommendations__grid","layout":{"type":"default"}} -->

			<!-- wp:group {"className":"cm-recommendation-card","layout":{"type":"default"}} -->
			<div class="wp-block-group cm-recommendation-card">

				<!-- wp:post-featured-image {"isLink":true,"className":"cm-recommendation-card__media"} /-->

				<!-- wp:group {"className":"cm-recommendation-card__body","layout":{"type":"default"}} -->
				<div class="wp-block-group cm-recommendation-card__body">

					<!-- wp:paragraph {"className":"cm-recommendation-card__label"} -->
					<p class="cm-recommendation-card__label">POLECAMY</p>
					<!-- /wp:paragraph -->

					<!-- wp:post-title {"isLink":true,"level":3} /-->

					<!-- wp:post-excerpt {"className":"cm-recommendation-card__description","moreText":""} /-->

					<!-- wp:read-more {"content":"Sprawdź szczegóły →","className":"cm-recommendation-card__link"} /-->

				</div>
				<!-- /wp:group -->

			</div>
			<!-- /wp:group -->

		<!-- /wp:post-template -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->
