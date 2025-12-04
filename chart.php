<?php include 'header.php'; ?>

<div class="container">
    <h2>Application Flow Chart</h2>
    <div class="info-box">
        The chart below shows the overall flow and pages/screens in the Library Management System,
        including Login, Dashboard, Maintenance, Transactions and Reports.
    </div>

    <style>
        .flow-wrapper{
            margin-top:20px;
            display:flex;
            flex-direction:column;
            gap:20px;
            align-items:center;
            font-size:14px;
        }
        .flow-row{
            display:flex;
            gap:20px;
            flex-wrap:wrap;
            justify-content:center;
            align-items:flex-start;
        }
        .flow-box{
            border:1px solid #999;
            border-radius:6px;
            padding:10px 15px;
            min-width:160px;
            text-align:center;
            background:#fafafa;
        }
        .flow-title{
            font-weight:bold;
            margin-bottom:5px;
        }
        .arrow-down{
            font-size:18px;
            margin:0;
        }
        .arrow-right{
            font-size:18px;
            margin:0 5px;
        }
        .role-note{
            font-size:12px;
            margin-top:5px;
            color:#555;
        }
        .sub-list{
            text-align:left;
            margin-top:6px;
            padding-left:18px;
        }
    </style>

    <div class="flow-wrapper">

        <!-- LOGIN -->
        <div class="flow-box">
            <div class="flow-title">Login</div>
            <div>login.php</div>
            <div class="sub-list">
                <ul>
                    <li>Admin (full access)</li>
                    <li>User (no Maintenance)</li>
                </ul>
            </div>
        </div>

        <div class="arrow-down">⬇</div>

        <!-- DASHBOARD -->
        <div class="flow-box">
            <div class="flow-title">Dashboard</div>
            <div>dashboard.php</div>
            <div class="sub-list">
                <ul>
                    <li>Shows stats & search</li>
                    <li>Links to all modules</li>
                </ul>
            </div>
        </div>

        <div class="arrow-down">⬇</div>

        <!-- MAIN MODULE ROW -->
        <div class="flow-row">

            <!-- MAINTENANCE -->
            <div class="flow-box">
                <div class="flow-title">Maintenance (Admin Only)</div>
                <div class="sub-list">
                    <ul>
                        <li><b>Membership</b><br>maintenance_membership.php
                            <ul>
                                <li>Add Membership</li>
                                <li>Update / Extend / Cancel</li>
                            </ul>
                        </li>
                        <li><b>Books / Movies</b><br>maintenance_items.php
                            <ul>
                                <li>Add Book / Movie</li>
                                <li>Update Book / Movie</li>
                                <li>All Books / Movies (books_list.php)</li>
                            </ul>
                        </li>
                        <li><b>User Management</b><br>user_management.php
                            <ul>
                                <li>New User</li>
                                <li>Existing User</li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="role-note">Admin can access</div>
            </div>

            <!-- TRANSACTIONS -->
            <div class="flow-box">
                <div class="flow-title">Transactions</div>
                <div class="sub-list">
                    <ul>
                        <li><b>Book Issue</b><br>transactions_issue.php
                            <ul>
                                <li>Select Book (dropdown)</li>
                                <li>Author auto-populated</li>
                                <li>Select Member (dropdown)</li>
                                <li>Issue Date ≥ Today</li>
                                <li>Return Date = Issue + 15 days max</li>
                            </ul>
                        </li>
                        <li class="arrow-down">⬇</li>
                        <li><b>Return Book</b><br>transactions_return.php
                            <ul>
                                <li>Select Serial No (issued only)</li>
                                <li>Select Membership No</li>
                                <li>Book, Author, Issue Date auto-filled</li>
                                <li>Return Date editable</li>
                            </ul>
                        </li>
                        <li class="arrow-down">⬇</li>
                        <li><b>Pay Fine</b><br>fine_pay.php
                            <ul>
                                <li>Shows fine (if any)</li>
                                <li>Fine Paid checkbox</li>
                                <li>Completes return</li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="role-note">Admin & User can access</div>
            </div>

            <!-- REPORTS -->
            <div class="flow-box">
                <div class="flow-title">Reports</div>
                <div>reports.php</div>
                <div class="sub-list">
                    <ul>
                        <li>Books Issued Today</li>
                        <li>Overdue Books</li>
                        <li>Active Memberships</li>
                        <li>Memberships Expiring This Month</li>
                    </ul>
                </div>
                <div class="role-note">Admin & User can access</div>
            </div>
        </div>

        <!-- LOGOUT -->
        <div class="arrow-down">⬇</div>
        <div class="flow-box">
            <div class="flow-title">Logout</div>
            <div>logout.php</div>
        </div>

    </div>
</div>