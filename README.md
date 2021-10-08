Tock
====

Simple scheduling and reports for Let's Rage LLC.

- Runs on AWS-hosted MySQL database
- Stores user records containing:
  - Contact information
  - Weekly schedule
  - Clock in/out events
  - User authentication
- Access control only allows clocking in/out from whitelisted IP addresses
- Supervisor/admin privileges
- Generated report page with:
  - Employee contact info
  - Scheduled hours
  - Clock in/out events
