<?php
/**
 * Title: Newsletter
 * Slug: calymyk-new/newsletter
 * Categories: call-to-action
 * Inserter: false
 */
?>

<!-- wp:group {"className":"cm-newsletter","layout":{"type":"default"}} -->
<div class="wp-block-group cm-newsletter">

	<!-- wp:group {"className":"cm-newsletter__grid cm-grid","layout":{"type":"default"}} -->
	<div class="wp-block-group cm-newsletter__grid cm-grid">

		<!-- wp:group {"className":"cm-span-7 cm-newsletter__content","layout":{"type":"default"}} -->
		<div class="wp-block-group cm-span-7 cm-newsletter__content">
			<!-- wp:paragraph {"className":"cm-eyebrow"} -->
			<p class="cm-eyebrow">NEWSLETTER</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">Dobre rzeczy. Bez spamu.</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"cm-newsletter__description"} -->
			<p class="cm-newsletter__description">Raz na jakiś czas wysyłamy najciekawsze narzędzia, praktyczne materiały i rzeczy warte sprawdzenia. Bez zapychania skrzynki.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"cm-span-5 cm-newsletter__form-wrap","layout":{"type":"default"}} -->
		<div class="wp-block-group cm-span-5 cm-newsletter__form-wrap">
			<!-- wp:form {"submissionMethod":"email","className":"cm-newsletter__form"} -->
			<form class="wp-block-form cm-newsletter__form">
				<!-- wp:form-input {"type":"email","name":"email","label":"Twój adres e-mail","required":true,"placeholder":"ty@example.com","inlineLabel":true} -->
				<div class="wp-block-form-input"><label class="wp-block-form-input__label"><span class="wp-block-form-input__label-content">Twój adres e-mail</span><input class="wp-block-form-input__input" type="email" name="email" required aria-required="true" placeholder="ty@example.com"/></label></div>
				<!-- /wp:form-input -->

				<!-- wp:form-submit-button -->
				<div class="wp-block-form-submit-button">
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button {"tagName":"button","type":"submit"} -->
						<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Zapisuję się →</button></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:form-submit-button -->

				<!-- wp:paragraph {"className":"cm-newsletter__legal"} -->
				<p class="cm-newsletter__legal">Wysyłając formularz, zgadzasz się na kontakt mailowy w sprawie newslettera.</p>
				<!-- /wp:paragraph -->
			</form>
			<!-- /wp:form -->
		</div>
		<!-- /wp:group -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
