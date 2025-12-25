# File Converter

A powerful Laravel-based web application for converting files between different formats with flexible schema-based transformations.

## Features

- **JSON to CSV Conversion** with custom schema mapping
- **Flexible Data Extraction** using path expressions (`$item.name`, `$item.0`)
- **Data Transformations** with operation pipelines (split, etc.)
- **Type Conversion** (string, integer, float, boolean)
- **Real-time Validation** with client-side JSON validation
- **Session-based Instances** for managing multiple conversions
- **Background Processing** with Laravel queues
- **File Management** with automatic cleanup

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (for frontend assets)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd converterv2
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=converter
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Configure Queue (Optional but Recommended)

For background processing, configure your queue driver in `.env`:

```env
QUEUE_CONNECTION=database
```

Then run the queue worker:

```bash
php artisan queue:work
```

### 7. Build Frontend Assets

```bash
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## How It Works

### Architecture Overview

```
User Upload → Instance Creation → Configuration → Processing → Download
```

### Flow Diagram

```
┌──────────────────┐
│  Create Instance │
│  (Select Schema) │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Upload File &   │
│  Configure       │
│  Settings        │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Start           │
│  Conversion      │
│  (Background Job)│
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Process:        │
│  1. Read Source  │
│  2. Extract Data │
│  3. Transform    │
│  4. Convert      │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Download        │
│  Converted File  │
└──────────────────┘
```

### Components

#### 1. **Instance**
- Represents a single conversion task
- Stores: schema type, settings, file paths, status
- Lifecycle: Created → Configured → Pending → Processing → Completed/Failed

#### 2. **Schema**
- Defines how to convert from source format to target format
- JSON to CSV schema maps JSON fields to CSV columns
- Includes reading and converting settings

#### 3. **Extraction**
- Reads source file
- Extracts data based on schema
- Applies transformations
- Converts types

#### 4. **Conversion**
- Takes extracted data
- Formats according to target format
- Generates output file

## JSON to CSV Conversion

### Schema Structure

A schema is a JSON array where each element defines a CSV column:

```json
[
    {
        "label": "Column Name",
        "type": "string",
        "value": "$item.path.to.field",
        "operations": [...]
    }
]
```

#### Field Definitions

- **label** (required): The column header in CSV
- **type** (required): Data type - `string`, `integer`, `float`, `boolean`
- **value** (required): Path to extract data from JSON
- **operations** (optional): Array of transformations to apply

### Value Paths

Extract data using path expressions:

```json
// Associative array
"value": "$item.name"          // → data.name
"value": "$item.user.email"    // → data.user.email

// Indexed array
"value": "$item.0"             // → data[0]
"value": "$item.1"             // → data[1]

// Mixed
"value": "$item.users.0.name"  // → data.users[0].name
```

### Operations

Transform extracted values using operations:

#### Split Operation

Split a string by delimiter and extract part by index:

```json
{
    "label": "Street",
    "type": "string",
    "value": "$item.address",
    "operations": [
        {
            "type": "split",
            "properties": {
                "delimiter": ",",
                "index": 0
            }
        }
    ]
}
```

**Example:**
- Input: `"123 Main St, Suite 400, New York, 10001"`
- Delimiter: `","`
- Index: `0`
- Output: `"123 Main St"`

### Complete Example: Address Splitting

#### Input JSON:
```json
[
    {
        "name": "John Doe",
        "address": "123 Main St, Suite 400, New York, 10001"
    }
]
```

#### Schema:
```json
[
    {
        "label": "Name",
        "type": "string",
        "value": "$item.name",
        "operations": []
    },
    {
        "label": "Street",
        "type": "string",
        "value": "$item.address",
        "operations": [
            {
                "type": "split",
                "properties": {
                    "delimiter": ",",
                    "index": 0
                }
            }
        ]
    },
    {
        "label": "Suite",
        "type": "string",
        "value": "$item.address",
        "operations": [
            {
                "type": "split",
                "properties": {
                    "delimiter": ",",
                    "index": 1
                }
            }
        ]
    },
    {
        "label": "City",
        "type": "string",
        "value": "$item.address",
        "operations": [
            {
                "type": "split",
                "properties": {
                    "delimiter": ",",
                    "index": 2
                }
            }
        ]
    },
    {
        "label": "Zipcode",
        "type": "string",
        "value": "$item.address",
        "operations": [
            {
                "type": "split",
                "properties": {
                    "delimiter": ",",
                    "index": 3
                }
            }
        ]
    }
]
```

#### Output CSV:
```csv
Name,Street,Suite,City,Zipcode
John Doe,123 Main St,Suite 400,New York,10001
```

## Converting Settings

### CSV Output Options

- **Separator**: Field delimiter (`,`, `;`, `\t`)
- **Enclosure**: Field enclosure character (`"`, `'`)
- **Escape**: Escape character for special chars (`\`, `"`)
- **EOL**: End of line character (`\n`, `\r\n`)

**Note:** Use escape sequences like `\n`, `\t` - they will be automatically converted.

### Example Configuration

```json
{
    "separator": ",",
    "enclosure": "\"",
    "escape": "\\",
    "eol": "\\n"
}
```

## Usage Guide

### 1. Create New Instance

1. Navigate to homepage
2. Click "Create New Instance"
3. Select schema type (e.g., "JSON to CSV")
4. Click "Create"

### 2. Configure Instance

1. Upload source file (JSON, CSV, etc.)
2. Configure reading settings:
   - Define schema (JSON format)
   - Use "Validate & Format" button to check syntax
3. Configure converting settings:
   - Set separator, enclosure, escape, EOL
4. Click "Save Configuration"

**Note:** Client-side validation runs automatically before saving.

### 3. Start Conversion

1. Once configured, click "Start Conversion"
2. Conversion runs in background
3. Page shows progress indicator

### 4. Download Result

1. When complete, "Download Converted File" button appears
2. Click to download the converted file

## Validation

### Client-Side Validation

- **Real-time JSON validation** on blur
- **Visual feedback**: Green border (valid), Red border (invalid)
- **Error messages** with specific issues
- **Auto-formatting** of valid JSON
- **Pre-submit validation** prevents invalid data

### Server-Side Validation

- **File type validation**: Ensures correct file extensions
- **File size validation**: Max 20MB
- **Schema validation**: Validates JSON structure
- **Settings validation**: Validates all configuration options
- **Operation validation**: Only allowed operations execute

## File Management

### Storage Structure

```
storage/app/
├── instances/
│   └── {instance_id}/
│       ├── original.{ext}      # Uploaded source file
│       └── converted.{ext}     # Generated output file
```

### Automatic Cleanup

- **Old instances** automatically removed (configurable)
- **Session cleanup** removes associated instances
- **Failed conversions** files retained for debugging

Command to manually clean old instances:
```bash
php artisan instances:remove-outdated
```

## Configuration

### Queue Configuration

Edit `config/queue.php` to configure background processing:

```php
'default' => env('QUEUE_CONNECTION', 'database'),
```

### File Size Limits

Edit `config/converting.php`:

```php
'max_file_size' => 20 * 1024, // 20MB in KB
```

### Instance Retention

Configure how long instances are kept:

```php
'instance_retention_days' => 7, // Keep for 7 days
```

## Extending

### Adding New Operations

1. **Add to whitelist** in `JsonSchemaExtractor.php`:
```php
private const ALLOWED_OPERATIONS = [
    'split',
    'your_operation', // Add here
];
```

2. **Define required properties**:
```php
private const OPERATION_REQUIRED_PROPERTIES = [
    'your_operation' => ['property1', 'property2'],
];
```

3. **Implement operation method**:
```php
private function operationYourOperation($value, OperationData $operation)
{
    // Your logic here
    return $transformedValue;
}
```

4. **Add to match expression**:
```php
return match ($operation->type) {
    'split' => $this->operationSplit($value, $operation),
    'your_operation' => $this->operationYourOperation($value, $operation),
    default => $value,
};
```

### Adding New Schema Types

1. Create new converting strategy in `app/Services/Schemas/{Type}/`
2. Implement `ConvertingStrategyInterface`
3. Add enum value to `ConvertingSchemaType`
4. Register in `SchemaFactory`

## Troubleshooting

### Common Issues

**Queue not processing:**
```bash
# Make sure queue worker is running
php artisan queue:work

# Check failed jobs
php artisan queue:failed
```

**File upload fails:**
- Check file size limits in `php.ini`:
  - `upload_max_filesize`
  - `post_max_size`
- Check storage permissions

**Conversion errors:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify JSON schema is valid
- Ensure source file format matches schema type

## Security

- **No code execution**: Only whitelisted operations allowed
- **Injection prevention**: Structured data, not parsed expressions
- **File type validation**: Only allowed extensions accepted
- **Session isolation**: Users only access their own instances
- **Automatic cleanup**: Old files removed automatically

## Performance

- **Background processing**: Long conversions don't block UI
- **Streaming responses**: Large files downloaded efficiently
- **Database indexing**: Fast instance lookups
- **File caching**: Reduced disk I/O

## License

[MIT License](https://opensource.org/licenses/MIT)

## Support

For issues and questions:
- Create an issue in the repository
- Contact the development team

## Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

---

Built with Laravel and generated with [Claude Code](https://claude.com/claude-code)
