<?php
/**
* Function to display user searches admin page
*/
function user_searches_admin_page() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'user_searches';

    // Get the total number of searches
    $total_searches = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

    // Get the count of each search query
    $results = $wpdb->get_results( "SELECT search_query, COUNT(*) as count FROM $table_name GROUP BY search_query ORDER BY count DESC", ARRAY_A );

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
                echo '<td><div class="percentage-bar" style="width:' . $percentage . '%;"></div></td></tr>';
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