<div class="tiny-account-status" id="tiny-account-status" data-state="complete">
	<div class="status <?php echo $status->ok ? ( $status->pending ? 'status-pending' : 'status-success' ) : 'status-failure'; ?>">
		<p class="status"><?php
		if ( $status->ok ) {
			if ( isset( $status->message ) ) {
				echo esc_html( $status->message, 'tiny-compress-images' );
			} else {
				echo esc_html__( 'Your account is connected', 'tiny-compress-images' );
				if ( ! defined( 'TINY_API_KEY' ) ) {
					echo ' <a href="#" id="change-key">';
					echo esc_html__( '(change key)', 'tiny-compress-images' );
					echo '</a>';
				}
			}
		} else {
			echo esc_html__( 'Connection unsuccessful', 'tiny-compress-images' );
		}
		?></p>
		<p><?php
		if ( $status->ok ) {
			$strong = array(
				'strong' => array(),
			);
			$compressions = self::get_compression_count();
			$remaining_credits = self::get_remaining_credits();
			/* It is not possible to check if a subscription is free or flexible. */
			if ( self::limit_reached() ) {
				$link = '<a href="https://tinypng.com/dashboard/api" target="_blank">' . esc_html__( 'TinyPNG API account', 'tiny-compress-images' ) . '</a>';
				esc_html_e(
					'You have reached your free limit this month.',
					'tiny-compress-images'
				);
				echo '<br>';
				/* translators: %s: link saying TinyPNG API account */
				printf( esc_html__(
					'If you need to compress more images you can upgrade your %s.',
					'tiny-compress-images'
				), $link );
			}
			if ( self::is_on_free_plan() ) {
				/* translators: %s: number of remaining credits */
				printf( wp_kses( __(
					'You are on a <strong>free plan</strong> with <strong>%s compressions left</strong> this month.', // WPCS: Needed for proper translation.
					'tiny-compress-images'
				), $strong ), $remaining_credits );
			} elseif ( ! $status->pending ) {
				/* translators: %s: number of compressions */
				printf( esc_html__(
					'You have made %s compressions this month.',
					'tiny-compress-images'
				), $compressions );
			}
		} else {
			if ( isset( $status->message ) ) {
				echo esc_html__( 'Error', 'tiny-compress-images' ) . ': ';
				echo esc_html( $status->message, 'tiny-compress-images' );
			} else {
				esc_html__(
					'API status could not be checked, enable cURL for more information',
					'tiny-compress-images'
				);
			}
		} // End if().
		?></p>
		<p><?php
		if ( defined( 'TINY_API_KEY' ) ) {
			/* translators: %s: wp-config.php */
			echo sprintf( esc_html__(
				'The API key has been configured in %s',
				'tiny-compress-images'
			), 'wp-config.php' );
		}
		?></p>
	</div>

	<div class="update" style="display: none">
		<h4><?php echo esc_html__( 'Change your API key', 'tiny-compress-images' ); ?></h4>
		<p class="introduction"><?php
			$link = sprintf( '<a href="https://tinypng.com/dashboard/api" target="_blank">%s</a>',
				esc_html__( 'API dashboard', 'tiny-compress-images' )
			);
			/* translators: %s: link saying API dashboard */
			printf( esc_html__(
				'Enter your API key. If you have lost your key, go to your %s to retrieve it.',
				'tiny-compress-images'
			), $link );
		?></p>

		<input type="text" id="tinypng_api_key"
			name="tinypng_api_key" size="35" spellcheck="false"
			value="<?php echo esc_attr( $key ); ?>">

		<button class="button button-primary" data-tiny-action="update-key"><?php
			echo esc_html__( 'Save' );
		?></button>

		<p class="message"></p>

		<p><a href="#" id="cancel-change-key"><?php
			echo esc_html__( 'Cancel' );
		?></a></p>
	</div><?php
	if ( self::is_on_free_plan() ) { ?>
		<div class="upgrade">
			<p>
			<?php echo esc_html__(
				'Remove all limitations? Visit your TinyPNG dashboard to upgrade your account.',
				'tiny-compress-images'
			); ?>
			</p>
			<div class="button-container">
				<a href="https://tinypng.com/developers/upgrade?email_address=<?php echo self::get_email_address(); ?>" target="_blank" class="button button-primary button-hero upgrade-account">
				<?php echo esc_html__( 'Upgrade account', 'tiny-compress-images' ); ?>
				</a>
			</div>
		</div>
	<?php } ?>
</div>
