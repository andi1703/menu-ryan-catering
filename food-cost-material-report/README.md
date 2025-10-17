# Food Cost Material Report

This project is designed to generate reports on raw material requirements for a food cost application. It provides functionalities to manage material data, generate various reports, and export them in different formats.

## Project Structure

```
food-cost-material-report
├── src
│   ├── controllers
│   │   ├── ReportController.php
│   │   └── MaterialController.php
│   ├── models
│   │   ├── MaterialReport.php
│   │   ├── MenuModel.php
│   │   └── BahanModel.php
│   ├── views
│   │   ├── reports
│   │   │   ├── material_requirement.php
│   │   │   ├── weekly_summary.php
│   │   │   └── cost_analysis.php
│   │   ├── layouts
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   └── partials
│   │       ├── material_table.php
│   │       └── chart_section.php
│   ├── assets
│   │   ├── css
│   │   │   ├── report.css
│   │   │   └── table.css
│   │   ├── js
│   │   │   ├── report.js
│   │   │   ├── chart.js
│   │   │   └── export.js
│   │   └── images
│   └── config
│       ├── database.php
│       └── report_config.php
├── exports
│   ├── excel
│   ├── pdf
│   └── csv
├── logs
├── composer.json
├── config.php
└── README.md
```

## Features

- **Material Management**: Add, update, and delete materials.
- **Report Generation**: Generate reports for material requirements, weekly summaries, and cost analysis.
- **Export Options**: Export reports in Excel, PDF, and CSV formats.
- **User-Friendly Interface**: Easy navigation and clear presentation of data.

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd food-cost-material-report
   ```
3. Install dependencies using Composer:
   ```
   composer install
   ```
4. Configure the database settings in `src/config/database.php`.
5. Run the application on a local server.

## Usage

- Access the application through your web browser.
- Use the navigation to manage materials and generate reports.
- Utilize the export functionality to download reports in your desired format.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.