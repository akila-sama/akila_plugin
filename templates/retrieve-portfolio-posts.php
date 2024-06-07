<?php
/**
 * Retrieve Portfolio Posts Template
 *
 * This template file is used to display the retrieved portfolio posts in a tabular format.
 *
 * @package Akila_Portfolio
 */

if ( $query->have_posts() ) :
	?>
<table>
	<thead>
		<tr>
			<th><?php echo esc_html__( 'Title', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Client Name', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Company Name', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Email', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Phone', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Address', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'email sent', 'akila-portfolio' ); ?></th>
			<th><?php echo esc_html__( 'Action', 'akila-portfolio' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			?>
		<tr>
			<td><?php echo esc_html( get_the_title() ); ?></td>
			<td><?php echo esc_html( get_post_meta( get_the_ID(), 'client_name', true ) ); ?></td>
			<td><?php echo esc_html( get_post_meta( get_the_ID(), 'company_name', true ) ); ?></td>
			<td><?php echo esc_html( get_post_meta( get_the_ID(), 'email', true ) ); ?></td>
			<td><?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></td>
			<td><?php echo esc_html( get_post_meta( get_the_ID(), 'address', true ) ); ?></td>
			<td><?php echo esc_html( get_the_date() . ' ' . get_the_time() ); ?></td>			<td><button class="delete-portfolio-post" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>"><?php esc_html_e( 'Delete', 'akila-portfolio' ); ?></button></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>

	<?php
else :
	// If no portfolio posts found, display a message
	echo '<p>' . esc_html__( 'No portfolio posts found.', 'akila-portfolio' ) . '</p>';
endif;

wp_reset_postdata();
?>
