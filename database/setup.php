<?php
echo "<h2>Setting up Auction System Database</h2>";

// Step 1: Check/Create Database
echo "<h3>Step 1: Database Check</h3>";
require_once 'check_database.php';

// Step 2: Create Tables
echo "<h3>Step 2: Creating Tables</h3>";
require_once 'setup_auction_tables.php';

echo "<h3>Setup Complete!</h3>";
echo "<p>You can now return to the <a href='../auction/'>Auction Platform</a></p>"; 