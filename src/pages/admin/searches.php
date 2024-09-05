<?php
/**
 * Displays the admin page to find searches
 *
 * @package Search_Tracker
 */

/**
 * Function to display user searches admin page
 */
function user_searches_admin_page() {
	$results = array();

	$unique_searches = get_unique_searches();
	$total_searches  = get_search_count();

	foreach ( $unique_searches as $search ) {
		$results[] = array(
			'search_query' => $search->search_query,
			'count'        => get_search_count( $search->search_query ),
		);
	}

	echo '<div class="wrap">';
		echo '<h1>User Searches</h1>';

	if ( empty( $results ) ) {
		echo '<p>No user searches found.</p>';
		return;
	}

		echo '<table>';
			echo '<tr><th>Search Query</th><th>Count</th><th>Percentage</th><th>Bar</th></tr>';

	foreach ( $results as $row ) {
		$percentage = round( ( $row['count'] / $total_searches ) * 100, 2 );
		echo '<tr><td>' . esc_html( $row['search_query'] ) . '</td><td>' . esc_html( $row['count'] ) . '</td><td>' . esc_html( $percentage ) . '%</td>';
		echo '<td><div class="percentage-bar" style="width:' . esc_html( $percentage ) . '%;"></div></td></tr>';
	}

			echo '</table>';
		echo '</div>';

	echo '<style>table {
            width: 100%;
            max-width: 800px;
        } .percentage-bar {
              height: 20px;
              background-color: #4CAF50;
          }
    
        .wrap {
            font-family: Arial, sans-serif;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
        }
    
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    
        th {
            background-color: #f7f7f7;
        }
    
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    
        .button {
            background-color: #007cba;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border: none;
            border-radius: 3px;
        }
    
        .button:hover {
            background-color: #005fa3;
        }
    </style>';
}

/**
 * Function to add user searches to admin menu
 */
function user_searches_admin_menu() {
	add_menu_page( 'User Searches', 'User Searches', 'manage_options', 'user-searches', 'user_searches_admin_page' );
}

add_action( 'admin_menu', 'user_searches_admin_menu' );
