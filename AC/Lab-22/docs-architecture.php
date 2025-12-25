<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Architecture Overview - Lab 22</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #164e63 100%);
            color: #e2e8f0;
            min-height: 100vh;
            line-height: 1.7;
        }
        .header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(6, 182, 212, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo { font-size: 1.5rem; font-weight: bold; color: #22d3ee; }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: #22d3ee;
            text-decoration: none;
            border-radius: 6px;
        }
        .container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
        h1 { color: #22d3ee; font-size: 2rem; margin-bottom: 0.5rem; }
        .subtitle { color: #64748b; margin-bottom: 2rem; }
        .card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .card h2 { color: #22d3ee; margin-bottom: 1rem; }
        .card h3 { color: #f59e0b; margin: 1.5rem 0 1rem; }
        .card p { color: #94a3b8; margin-bottom: 1rem; }
        .diagram {
            background: #0d1117;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .diagram pre {
            color: #e2e8f0;
            font-family: monospace;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        .flow-step {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 8px;
        }
        .flow-step .number {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        .flow-step .content h4 { color: #22d3ee; margin-bottom: 0.25rem; }
        .flow-step .content p { color: #94a3b8; margin: 0; }
        .entity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .entity {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            padding: 1.25rem;
        }
        .entity h4 { color: #22d3ee; margin-bottom: 0.75rem; }
        .entity ul { list-style: none; color: #94a3b8; font-size: 0.9rem; }
        .entity li { padding: 0.25rem 0; }
        .entity .key { color: #f59e0b; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 0.25rem;
        }
        .btn-primary { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
        .btn-secondary { background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); color: #22d3ee; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="docs.php">← Docs</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="login.php">🔑 Login</a>
        </nav>
    </header>

    <div class="container">
        <h1>🏗️ Architecture Overview</h1>
        <p class="subtitle">Understanding the ride-sharing application structure</p>

        <div class="card">
            <h2>📊 System Architecture</h2>
            <div class="diagram">
<pre>
┌─────────────────────────────────────────────────────────────────────────────┐
│                           RIDEKEA ARCHITECTURE                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                   │
│  │   Passenger  │    │    Driver    │    │    Admin     │                   │
│  │     App      │    │     App      │    │    Panel     │                   │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘                   │
│         │                   │                    │                           │
│         └─────────┬─────────┴────────────────────┘                          │
│                   │                                                          │
│                   ▼                                                          │
│         ┌─────────────────────┐                                             │
│         │    API Gateway      │                                             │
│         │  (Authentication)   │ ◄─── Session/Token Validation               │
│         └──────────┬──────────┘                                             │
│                    │                                                         │
│         ┌──────────┴──────────┐                                             │
│         │                     │                                              │
│    ┌────▼────┐          ┌─────▼─────┐                                       │
│    │Booking  │          │  Bids     │                                       │
│    │Service  │          │ Service   │                                       │
│    └────┬────┘          └─────┬─────┘                                       │
│         │                     │                                              │
│         └──────────┬──────────┘                                             │
│                    │                                                         │
│         ┌──────────▼──────────┐                                             │
│         │     MySQL DB        │                                             │
│         │   (ac_lab22)        │                                             │
│         └─────────────────────┘                                             │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
</pre>
            </div>
        </div>

        <div class="card">
            <h2>📁 Database Entity Relationships</h2>
            <div class="diagram">
<pre>
┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│    users     │         │   bookings   │         │     bids     │
├──────────────┤         ├──────────────┤         ├──────────────┤
│ user_id (PK) │◄───────┤│passenger_id  │        │ bid_id (PK)  │
│ username     │         │ booking_id(PK)│◄───────┤│booking_id    │
│ full_name    │         │ driver_id    │         │ driver_id    │
│ phone        │◄────────┤│(assigned)    │         │ driver_name  │
│ email        │         │ pickup_addr  │         │ driver_phone │
│ role         │         │ dropoff_addr │         │ bid_amount   │
│ is_driver    │         │ pickup_lat   │         │ vehicle_type │
│ password     │         │ pickup_lng   │         │ vehicle_no   │
└──────────────┘         │ dropoff_lat  │         │ driver_rating│
                         │ dropoff_lng  │         │ status       │
                         │ est_fare     │         └──────────────┘
                         │ status       │                  │
                         │ trip_no      │                  │
                         └──────┬───────┘                  │
                                │                          │
                         ┌──────▼───────┐                  │
                         │ bids_config  │◄─────────────────┘
                         ├──────────────┤
                         │config_id (PK)│
                         │booking_id(FK)│
                         │min_bid_amount│
                         │max_bid_amount│
                         │bid_increment │
                         │max_bids      │
                         └──────────────┘
</pre>
            </div>
        </div>

        <div class="card">
            <h2>🔄 Booking Flow</h2>
            
            <div class="flow-step">
                <div class="number">1</div>
                <div class="content">
                    <h4>Passenger Creates Booking</h4>
                    <p>Passenger selects pickup/dropoff locations and submits trip request</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="number">2</div>
                <div class="content">
                    <h4>System Generates Booking ID</h4>
                    <p>Unique booking_id created (BKG_xxxxxxxxxxxx format)</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="number">3</div>
                <div class="content">
                    <h4>Drivers Notified</h4>
                    <p>Nearby drivers receive booking notification with trip details</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="number">4</div>
                <div class="content">
                    <h4>Drivers Submit Bids</h4>
                    <p>Interested drivers submit their fare bids for the trip</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="number">5</div>
                <div class="content">
                    <h4>Passenger Views Bids</h4>
                    <p>⚠️ API returns ALL bid info without proper authorization check</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="number">6</div>
                <div class="content">
                    <h4>Passenger Accepts Bid</h4>
                    <p>Selected driver is assigned, trip begins</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>📦 Data Entities</h2>
            <div class="entity-grid">
                <div class="entity">
                    <h4>👤 User</h4>
                    <ul>
                        <li><span class="key">user_id</span> - Unique identifier</li>
                        <li>username - Login credential</li>
                        <li>full_name - Display name</li>
                        <li>phone - Contact number</li>
                        <li>email - Email address</li>
                        <li>role - passenger/driver</li>
                    </ul>
                </div>
                <div class="entity">
                    <h4>📦 Booking</h4>
                    <ul>
                        <li><span class="key">booking_id</span> - Unique trip ID</li>
                        <li>passenger_id - Who booked</li>
                        <li>pickup/dropoff - Locations</li>
                        <li>lat/lng - GPS coordinates</li>
                        <li>fare details - Pricing info</li>
                        <li>status - pending/completed</li>
                    </ul>
                </div>
                <div class="entity">
                    <h4>💰 Bid</h4>
                    <ul>
                        <li><span class="key">bid_id</span> - Unique bid ID</li>
                        <li>booking_id - Related trip</li>
                        <li>driver_id - Who bid</li>
                        <li>driver_name/phone - Contact</li>
                        <li>bid_amount - Offered fare</li>
                        <li>vehicle_number - Vehicle ID</li>
                    </ul>
                </div>
                <div class="entity">
                    <h4>⚙️ Bids Config</h4>
                    <ul>
                        <li><span class="key">config_id</span> - Config ID</li>
                        <li>booking_id - Related trip</li>
                        <li>min/max bid - Fare limits</li>
                        <li>auto_accept - Settings</li>
                        <li>driver_filters - Requirements</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>🔐 Security Boundaries (Missing!)</h2>
            <div class="diagram">
<pre>
┌─────────────────────────────────────────────────────────────────┐
│                    EXPECTED ACCESS CONTROL                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Passenger A ──► Own Bookings ✓    Other Bookings ✗             │
│                 Own Bids     ✓    Other Bids     ✗              │
│                                                                  │
│  Driver B    ──► Assigned Booking ✓    Random Booking ✗         │
│                 Own Bids         ✓    Other Bids     ✗          │
│                                                                  │
│  Admin      ──► All Bookings ✓    All Bids ✓                    │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                    ACTUAL ACCESS CONTROL (BROKEN!)               │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Any User   ──► ANY Booking ✓    ANY Bids ✓    ANY Config ✓     │
│                 (Just need valid booking_id!)                    │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
</pre>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="lab-description.php" class="btn btn-primary">🎯 Start Lab</a>
            <a href="docs-exploitation.php" class="btn btn-secondary">← Previous</a>
            <a href="docs.php" class="btn btn-secondary">📚 All Docs</a>
        </div>
    </div>
</body>
</html>
