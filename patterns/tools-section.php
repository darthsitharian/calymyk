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

	<!-- wp:group {"className":"cm-section-header","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-section-header">

		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">Narzędzia, które warto mieć pod ręką.</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Wybrane narzędzia do pracy, organizacji, tworzenia i codziennego działania.</p>
		<!-- /wp:paragraph -->

	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"40px"} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:query {"query":{"perPage":100,"postType":"tool","inherit":false},"className":"cm-tools-query"} -->
	<div class="wp-block-query cm-tools-query" data-cm-tools-carousel="true">

		<!-- wp:post-template {"className":"cm-tools-grid"} -->
			<!-- wp:group {"className":"cm-tool-card","layout":{"type":"default"}} -->
			<article class="wp-block-group cm-span-4 cm-tool-card">

				<!-- wp:post-featured-image {"isLink":true,"className":"cm-tool-card__image"} /-->

				<!-- wp:post-title {"isLink":true,"className":"cm-tool-card__title"} /-->

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
