<?php
/**
 * Submissions admin: contact messages + newsletter subscribers.
 *
 * Provides two admin screens (inbox + subscriber list), each with
 * CSV/Excel export and delete. This is where every contact and every
 * customer email is collected for future promotions.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the contact/subscriber admin screens and exports.
 */
class Guebel_Submissions {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_post_guebel_export_contacts', array( $this, 'export_contacts' ) );
		add_action( 'admin_post_guebel_export_subscribers', array( $this, 'export_subscribers' ) );
		add_action( 'admin_post_guebel_delete_contact', array( $this, 'delete_contact' ) );
		add_action( 'admin_post_guebel_delete_subscriber', array( $this, 'delete_subscriber' ) );
	}

	/**
	 * Register the top-level menu and sub-pages.
	 */
	public function add_menu() {
		add_menu_page(
			__( 'Guebel — Clientes', 'guebel-core' ),
			__( 'Guebel Clientes', 'guebel-core' ),
			'manage_options',
			'guebel-contacts',
			array( $this, 'render_contacts' ),
			'dashicons-email-alt',
			26
		);

		add_submenu_page(
			'guebel-contacts',
			__( 'Mensagens de Contacto', 'guebel-core' ),
			__( 'Mensagens de Contacto', 'guebel-core' ),
			'manage_options',
			'guebel-contacts',
			array( $this, 'render_contacts' )
		);

		add_submenu_page(
			'guebel-contacts',
			__( 'Emails de Clientes', 'guebel-core' ),
			__( 'Emails de Clientes', 'guebel-core' ),
			'manage_options',
			'guebel-subscribers',
			array( $this, 'render_subscribers' )
		);
	}

	/**
	 * Render the contact messages screen.
	 */
	public function render_contacts() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'guebel_contacts';
		$rows  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 500", ARRAY_A ); // phpcs:ignore
		$rows  = is_array( $rows ) ? $rows : array();
		?>
		<div class="wrap">
			<h1 style="display:flex;align-items:center;gap:16px;">
				<?php esc_html_e( 'Mensagens de Contacto', 'guebel-core' ); ?>
				<?php $this->export_button( 'guebel_export_contacts', __( 'Exportar para Excel (CSV)', 'guebel-core' ), count( $rows ) ); ?>
			</h1>
			<?php $this->notice_from_query(); ?>
			<p class="description"><?php esc_html_e( 'Todas as mensagens enviadas pelo formulário de contacto ficam guardadas aqui, mesmo que o email falhe.', 'guebel-core' ); ?></p>

			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Data', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Nome', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Email', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Telefone', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Mensagem', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Marketing', 'guebel-core' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $rows ) ) : ?>
						<tr><td colspan="7"><?php esc_html_e( 'Ainda não há mensagens.', 'guebel-core' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $rows as $r ) : ?>
							<tr>
								<td><?php echo esc_html( $r['created_at'] ); ?></td>
								<td><?php echo esc_html( $r['name'] ); ?></td>
								<td><a href="mailto:<?php echo esc_attr( $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a></td>
								<td><?php echo esc_html( $r['phone'] ); ?></td>
								<td style="max-width:340px;"><?php echo esc_html( wp_trim_words( $r['message'], 40 ) ); ?></td>
								<td><?php echo $r['marketing'] ? '✓' : '—'; ?></td>
								<td><?php $this->delete_link( 'guebel_delete_contact', (int) $r['id'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the newsletter subscribers screen.
	 */
	public function render_subscribers() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'guebel_newsletter';
		$rows  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 2000", ARRAY_A ); // phpcs:ignore
		$rows  = is_array( $rows ) ? $rows : array();
		?>
		<div class="wrap">
			<h1 style="display:flex;align-items:center;gap:16px;">
				<?php esc_html_e( 'Emails de Clientes', 'guebel-core' ); ?>
				<?php $this->export_button( 'guebel_export_subscribers', __( 'Exportar para Excel (CSV)', 'guebel-core' ), count( $rows ) ); ?>
			</h1>
			<?php $this->notice_from_query(); ?>
			<p class="description"><?php esc_html_e( 'Lista de todos os emails com consentimento de marketing (newsletter + opt-in no contacto). Exporte para Excel para as suas campanhas.', 'guebel-core' ); ?></p>

			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Data', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Email', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Nome', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Consentimento', 'guebel-core' ); ?></th>
						<th><?php esc_html_e( 'Data consentimento', 'guebel-core' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $rows ) ) : ?>
						<tr><td colspan="6"><?php esc_html_e( 'Ainda não há subscritores.', 'guebel-core' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $rows as $r ) : ?>
							<tr>
								<td><?php echo esc_html( $r['created_at'] ); ?></td>
								<td><a href="mailto:<?php echo esc_attr( $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a></td>
								<td><?php echo esc_html( $r['name'] ); ?></td>
								<td><?php echo ! empty( $r['consent'] ) ? '✓' : '—'; ?></td>
								<td><?php echo esc_html( $r['consent_date'] ); ?></td>
								<td><?php $this->delete_link( 'guebel_delete_subscriber', (int) $r['id'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Output an export button form.
	 *
	 * @param string $action Admin-post action.
	 * @param string $label  Button label.
	 * @param int    $count  Row count.
	 */
	private function export_button( $action, $label, $count ) {
		$disabled = $count > 0 ? '' : ' disabled';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin:0;">';
		echo '<input type="hidden" name="action" value="' . esc_attr( $action ) . '">';
		wp_nonce_field( $action );
		echo '<button type="submit" class="button button-primary"' . esc_attr( $disabled ) . '>' . esc_html( $label ) . '</button>';
		echo '</form>';
	}

	/**
	 * Output a delete link (as a mini form to keep it POST + nonce protected).
	 *
	 * @param string $action Admin-post action.
	 * @param int    $id     Row id.
	 */
	private function delete_link( $action, $id ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin:0;" onsubmit="return confirm(\'' . esc_js( __( 'Apagar este registo?', 'guebel-core' ) ) . '\');">';
		echo '<input type="hidden" name="action" value="' . esc_attr( $action ) . '">';
		echo '<input type="hidden" name="id" value="' . esc_attr( $id ) . '">';
		wp_nonce_field( $action . '_' . $id );
		echo '<button type="submit" class="button-link delete" style="color:#b32d2e;">' . esc_html__( 'Apagar', 'guebel-core' ) . '</button>';
		echo '</form>';
	}

	/**
	 * Show an admin notice based on a ?guebel_msg query arg.
	 */
	private function notice_from_query() {
		$msg = isset( $_GET['guebel_msg'] ) ? sanitize_key( wp_unslash( $_GET['guebel_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'deleted' === $msg ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Registo apagado.', 'guebel-core' ) . '</p></div>';
		}
	}

	/**
	 * Export contacts to CSV (Excel-friendly).
	 */
	public function export_contacts() {
		$this->guard( 'guebel_export_contacts' );
		global $wpdb;
		$table = $wpdb->prefix . 'guebel_contacts';
		$rows  = $wpdb->get_results( "SELECT created_at,name,email,phone,message,marketing FROM {$table} ORDER BY created_at DESC", ARRAY_A ); // phpcs:ignore
		$this->stream_csv(
			'guebel-contactos-' . gmdate( 'Y-m-d' ) . '.csv',
			array( 'Data', 'Nome', 'Email', 'Telefone', 'Mensagem', 'Marketing' ),
			is_array( $rows ) ? $rows : array()
		);
	}

	/**
	 * Export subscribers to CSV (Excel-friendly).
	 */
	public function export_subscribers() {
		$this->guard( 'guebel_export_subscribers' );
		global $wpdb;
		$table = $wpdb->prefix . 'guebel_newsletter';
		$rows  = $wpdb->get_results( "SELECT created_at,email,name,consent,consent_date,consent_ip FROM {$table} ORDER BY created_at DESC", ARRAY_A ); // phpcs:ignore
		$this->stream_csv(
			'guebel-emails-clientes-' . gmdate( 'Y-m-d' ) . '.csv',
			array( 'Data', 'Email', 'Nome', 'Consentimento', 'Data consentimento', 'IP' ),
			is_array( $rows ) ? $rows : array()
		);
	}

	/**
	 * Delete a contact row.
	 */
	public function delete_contact() {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$this->guard( 'guebel_delete_contact_' . $id );
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'guebel_contacts', array( 'id' => $id ), array( '%d' ) ); // phpcs:ignore
		$this->redirect_back( 'guebel-contacts' );
	}

	/**
	 * Delete a subscriber row.
	 */
	public function delete_subscriber() {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$this->guard( 'guebel_delete_subscriber_' . $id );
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'guebel_newsletter', array( 'id' => $id ), array( '%d' ) ); // phpcs:ignore
		$this->redirect_back( 'guebel-subscribers' );
	}

	/**
	 * Capability + nonce guard for admin-post actions.
	 *
	 * @param string $nonce_action Nonce action name.
	 */
	private function guard( $nonce_action ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sem permissões.', 'guebel-core' ) );
		}
		check_admin_referer( $nonce_action );
	}

	/**
	 * Redirect back to an admin page with a deleted notice.
	 *
	 * @param string $page Admin page slug.
	 */
	private function redirect_back( $page ) {
		wp_safe_redirect( admin_url( 'admin.php?page=' . $page . '&guebel_msg=deleted' ) );
		exit;
	}

	/**
	 * Stream rows as a UTF-8 CSV download (opens cleanly in Excel).
	 *
	 * @param string $filename Download filename.
	 * @param array  $header   Column headers.
	 * @param array  $rows     Data rows (assoc arrays).
	 */
	private function stream_csv( $filename, $header, $rows ) {
		nocache_headers();
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		$out = fopen( 'php://output', 'w' );
		// UTF-8 BOM so Excel shows accented characters correctly.
		echo "\xEF\xBB\xBF";
		fputcsv( $out, $header, ';' );
		foreach ( $rows as $row ) {
			$line = array();
			foreach ( $row as $value ) {
				$line[] = is_null( $value ) ? '' : preg_replace( '/[\r\n]+/', ' ', (string) $value );
			}
			fputcsv( $out, $line, ';' );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
}
