<?php
/**
 * Default Panels - shown when no front page content is set.
 *
 * @package Nuvora
 */

$options = nuvora_get_options();
?>

<!-- Intro Panel -->
<article id="intro">
	<h2 class="major"><?php esc_html_e( 'Intro', 'nuvora' ); ?></h2>
	<span class="image main"><img src="<?php echo esc_url( NUVORA_URI . '/images/pic01.jpg' ); ?>" alt="" /></span>
	<p><?php esc_html_e( 'Welcome! This is the Intro panel. Edit this content by setting a Front Page in Settings → Reading, then add Nuvora Panel blocks to your page.', 'nuvora' ); ?></p>
	<p><?php esc_html_e( 'You can also customise everything — title, background, social links, and more — under Appearance → Theme Options.', 'nuvora' ); ?></p>
</article>

<!-- Work Panel -->
<article id="work">
	<h2 class="major"><?php esc_html_e( 'Work', 'nuvora' ); ?></h2>
	<span class="image main"><img src="<?php echo esc_url( NUVORA_URI . '/images/pic02.jpg' ); ?>" alt="" /></span>
	<p><?php esc_html_e( 'This is the Work panel. Add your portfolio, projects, or services here.', 'nuvora' ); ?></p>
	<p><?php esc_html_e( 'Use the Nuvora Panel block in the Gutenberg editor to populate this section with your own content.', 'nuvora' ); ?></p>
</article>

<!-- About Panel -->
<article id="about">
	<h2 class="major"><?php esc_html_e( 'About', 'nuvora' ); ?></h2>
	<span class="image main"><img src="<?php echo esc_url( NUVORA_URI . '/images/pic03.jpg' ); ?>" alt="" /></span>
	<p><?php esc_html_e( 'Tell your story here. Who are you? What do you do? What makes you unique?', 'nuvora' ); ?></p>
</article>

<!-- Contact Panel -->
<article id="contact">
	<h2 class="major"><?php esc_html_e( 'Contact', 'nuvora' ); ?></h2>

	<?php if ( '1' === $options['show_contact_form'] ) : ?>
		<?php if ( isset( $_GET['contact'] ) && 'success' === $_GET['contact'] ) : ?>
			<div style="background:rgba(255,255,255,0.1);padding:1em;border-radius:4px;margin-bottom:1em;">
				<p style="margin:0;">✓ <?php esc_html_e( 'Your message has been sent!', 'nuvora' ); ?></p>
			</div>
		<?php elseif ( isset( $_GET['contact'] ) && 'error' === $_GET['contact'] ) : ?>
			<div style="background:rgba(255,100,100,0.2);padding:1em;border-radius:4px;margin-bottom:1em;">
				<p style="margin:0;">✗ <?php esc_html_e( 'Please fill in all fields with a valid email.', 'nuvora' ); ?></p>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="nuvora_contact">
			<?php wp_nonce_field( 'nuvora_contact', 'nuvora_contact_nonce' ); ?>
			<div class="fields">
				<div class="field half">
					<label for="contact_name"><?php esc_html_e( 'Name', 'nuvora' ); ?></label>
					<input type="text" name="contact_name" id="contact_name" required />
				</div>
				<div class="field half">
					<label for="contact_email"><?php esc_html_e( 'Email', 'nuvora' ); ?></label>
					<input type="email" name="contact_email" id="contact_email" required />
				</div>
				<div class="field">
					<label for="contact_message"><?php esc_html_e( 'Message', 'nuvora' ); ?></label>
					<textarea name="contact_message" id="contact_message" rows="4" required></textarea>
				</div>
			</div>
			<ul class="actions">
				<li><input type="submit" value="<?php esc_attr_e( 'Send Message', 'nuvora' ); ?>" class="primary" /></li>
				<li><input type="reset" value="<?php esc_attr_e( 'Reset', 'nuvora' ); ?>" /></li>
			</ul>
		</form>
	<?php endif; ?>

	<?php echo nuvora_social_icons(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</article>
