<?php
/**
 * Template File: tmpl-portfolio-posts.php
 *
 * Description: This template file generates HTML markup for displaying portfolio posts in a table format.
 * It utilizes Underscore.js templating to render the data received from the REST API endpoint.
 *
 * @package Akila_Portfolio
 * @subpackage Templates
 */
?>

<script type="text/html" id="tmpl-portfolio-post">
<table>
	<thead>
		<tr>
			<th><?php echo esc_html__( 'Title', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Client Name', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Company Name', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Email', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Phone', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Address', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Date', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Action', 'akila-portfolio' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<# _.each( data, function( item ) { #>
			<tr>
				<td>{{ item.title }}</td>
				<td>{{ item.client_name }}</td>
				<td>{{ item.company_name }}</td>
				<td>{{ item.email }}</td>
				<td>{{ item.phone }}</td>
				<td>{{ item.address }}</td>
				<td>{{ item.date }}</td>
				<td><button class="delete-portfolio-post" data-post-id="{{ item.post_id }}">{{ '<?php esc_html_e( 'Delete', 'akila-portfolio' ); ?>' }}</button></td>
			</tr>
		<# }) #>
	</tbody>
</table>
</script>
