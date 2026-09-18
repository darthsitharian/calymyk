<?php
/**
 * Title: Tools Section
 * Slug: calymyk-new/tools-section
 * Categories: calymyk
 * Inserter: true
 */
?>

<!-- wp:group {"className":"cm-tools-section","layout":{"type":"default"}} -->
<div class="wp-block-group cm-tools-section">

	<!-- wp:group {"className":"cm-section-header cm-grid","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-section-header cm-grid">

		<!-- wp:group {"className":"cm-span-8","layout":{"type":"default"}} -->
		<div class="wp-block-group cm-span-8">

			<!-- wp:paragraph {"className":"cm-eyebrow"} -->
			<p class="cm-eyebrow">NARZĘDZIA</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">Narzędzia, które warto mieć pod ręką.</h2>
			<!-- /wp:heading -->

		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"cm-span-4 cm-section-header__aside","layout":{"type":"default"}} -->
		<div class="wp-block-group cm-span-4 cm-section-header__aside">

			<!-- wp:paragraph -->
			<p>Wybrane narzędzia do pracy, organizacji, tworzenia i codziennego działania.</p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:group -->

	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"40px"} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:query {"query":{"perPage":3,"postType":"tool","inherit":false},"className":"cm-tools-query"} -->
	<div class="wp-block-query cm-tools-query">

		<!-- wp:post-template {"className":"cm-grid cm-tools-grid"} -->
			<!-- wp:group {"className":"cm-span-4 cm-tool-card","layout":{"type":"default"}} -->
			<article class="wp-block-group cm-span-4 cm-tool-card">

				<!-- wp:post-featured-image {"isLink":true,"className":"cm-tool-card__image"} /-->

				<!-- wp:post-terms {"term":"tool_category","className":"cm-tool-card__category"} /-->

				<!-- wp:post-title {"isLink":true} /-->

				<!-- wp:post-excerpt {"className":"cm-tool-card__description"} /-->

				<!-- wp:read-more {"content":"Zobacz narzędzie →","className":"cm-tool-card__link"} /-->

			</article>
			<!-- /wp:group -->
		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph -->
			<p>Nie ma jeszcze żadnych narzędzi.</p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->
