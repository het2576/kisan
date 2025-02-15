const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const app = express();
const path = require('path');

// Create MySQL connection
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'kisan_db'
});

// Connect to MySQL
connection.connect(error => {
    if (error) {
        console.error('Error connecting to the database:', error);
        return;
    }
    console.log('Successfully connected to database');
});

// Serve static files from the root directory
app.use(express.static(path.join(__dirname)));

// Serve static files from the uploads directory
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Add after creating the express app
app.use(cors({
  origin: 'http://localhost:4000', // Or your frontend origin
  credentials: true
}));

// Add before routes
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Add before other routes
app.use('/api/products', (req, res, next) => {
    const validSortOptions = ['price_high', 'price_low', 'newest', 'oldest'];
    
    if (req.query.sort && !validSortOptions.includes(req.query.sort)) {
        return res.status(400).json({ error: 'Invalid sort parameter' });
    }
    
    if (req.query.search && req.query.search.length > 100) {
        return res.status(400).json({ error: 'Search query too long' });
    }
    
    next();
});

// API endpoint to get products
app.get('/api/products', (req, res) => {
    const { category, search } = req.query;
    
    let query = `
        SELECT 
            p.product_id,
            COALESCE(p.name, 'Farm Fresh Produce') AS name,
            COALESCE(p.description, 'Premium quality agricultural product') AS description,
            COALESCE(p.price_per_kg, 0.00) AS price_per_kg,
            COALESCE(pi.image_url, '/images/placeholder.jpg') AS image_url,
            COALESCE(p.quantity_available, 0) AS quantity_available,
            COALESCE(p.unit, 'kg') AS unit,
            COALESCE(p.min_order_quantity, 1) AS min_order_quantity,
            COALESCE(p.farming_method, 'Traditional Farming') AS farming_method,
            COALESCE(p.delivery_options, 'Standard Delivery') AS delivery_options,
            COALESCE(p.is_organic, FALSE) AS is_organic
        FROM products p
        LEFT JOIN product_images pi 
            ON p.product_id = pi.product_id AND pi.is_primary = 1
    `;

    const queryParams = [];
    const whereClauses = [];

    if (category && category !== 'all') {
        whereClauses.push(`p.category_id = ?`);
        queryParams.push(category);
    }

    if (search) {
        whereClauses.push(`(p.name LIKE ? OR p.description LIKE ?)`);
        queryParams.push(`%${search}%`, `%${search}%`);
    }

    if (whereClauses.length > 0) {
        query += ' WHERE ' + whereClauses.join(' AND ');
    }

    console.log('Executing query:', query);
    console.log('With parameters:', queryParams);

    connection.query(query, queryParams, (error, results) => {
        if (error) {
            console.error('Database error:', error);
            return res.status(500).json({ 
                error: 'Database error',
                details: error.message
            });
        }

        try {
            const products = results.map(product => ({
                ...product,
                price_per_kg: product.price_per_kg.toLocaleString('en-IN', {
                    style: 'currency',
                    currency: 'INR',
                    minimumFractionDigits: 2
                }),
                quantity_available: `${product.quantity_available} ${product.unit}`,
                min_order_quantity: `${product.min_order_quantity} kg`,
                farming_method: product.farming_method,
                delivery_options: product.delivery_options
            }));

            res.json({ products });
        } catch (parseError) {
            console.error('Data parsing error:', parseError);
            res.status(500).json({ 
                error: 'Data processing error',
                details: parseError.message
            });
        }
    });
});

// Handle marketplace route
app.get('/marketplace', (req, res) => {
    res.sendFile(path.join(__dirname, 'marketplace.html'));
});

// Handle API requests to PHP files
app.use('/api', (req, res, next) => {
    // Let the PHP files handle API requests
    next();
});

// Add a health check endpoint
app.get('/health', (req, res) => {
    res.json({ status: 'ok', timestamp: new Date() });
});

// Add after routes
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ error: 'Something went wrong!' });
});

const PORT = process.env.PORT || 4000;
const server = app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});

server.on('error', error => {
    if (error.code === 'EADDRINUSE') {
        console.error(`Port ${PORT} is already in use!`);
        console.log('Try:');
        console.log(`1. Closing other running servers`);
        console.log(`2. Changing the PORT number in server.js`);
        console.log(`3. Waiting a few minutes for the port to become free`);
    } else {
        console.error('Server error:', error);
    }
    process.exit(1);
});
