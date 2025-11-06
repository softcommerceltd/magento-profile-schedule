# Cron Schedule Monitor - Usage Guide

## Overview
The Cron Schedule Monitor provides real-time monitoring of cron jobs for your PlentyONE integration with automatic refresh capabilities.

## Features

### 1. **Auto-Refresh (Live Updates)**
- Grid automatically refreshes every 5 seconds
- No manual page reload required
- Real-time status updates for running cron jobs

### 2. **Comprehensive Data Display**
- **Schedule ID**: Unique identifier for each cron task
- **Status**: Visual status indicator (Success, Error, Running, Pending, etc.)
- **Messages**: Detailed messages and logs (click to view in modal)
- **Job Code**: Profile type identifier
- **Created At**: When the task was created
- **Scheduled At**: When the task is scheduled to run
- **Executed At**: When the task started executing
- **Finished At**: When the task completed

### 3. **Advanced Filtering**
- Filter by job_code (specific profile types)
- Filter by status
- Filter by date ranges
- Full-text search across all fields

### 4. **Mass Actions**
- **Delete**: Remove completed/failed cron schedules
- **Schedule Cron Task**: Re-schedule tasks to pending status
- **Stop Cron Task**: Skip scheduled tasks

## Accessing the Monitor

### Admin Menu Path:
```
SoftCommerce → Profile Management → Cron Schedule Monitor
```

### Direct URL:
```
/admin/softcommerce/cronSchedule/index
```

## Auto-Refresh Configuration

### Default Settings:
- **Enabled**: Yes
- **Interval**: 5000ms (5 seconds)

### Customizing Refresh Interval

You can modify the refresh interval in the UI component XML:

**File**: `view/adminhtml/ui_component/softcommerce_cron_schedule_listing.xml`

```xml
<dataSource name="softcommerce_cron_schedule_listing_data_source"
            component="SoftCommerce_ProfileSchedule/js/grid/provider-auto-refresh">
    <argument name="data" xsi:type="array">
        <item name="config" xsi:type="array">
            <item name="autoRefreshInterval" xsi:type="number">5000</item> <!-- Change this value -->
            <item name="autoRefreshEnabled" xsi:type="boolean">true</item> <!-- Enable/disable -->
        </item>
    </argument>
</dataSource>
```

### Available Intervals:
- `3000` = 3 seconds (fast refresh)
- `5000` = 5 seconds (default, balanced)
- `10000` = 10 seconds (slower, less server load)
- `30000` = 30 seconds (minimal refresh)

### Disabling Auto-Refresh:
Set `autoRefreshEnabled` to `false`:
```xml
<item name="autoRefreshEnabled" xsi:type="boolean">false</item>
```

## JavaScript API

The auto-refresh provider exposes JavaScript methods for dynamic control:

```javascript
// Access the provider
var provider = registry.get('softcommerce_cron_schedule_listing.softcommerce_cron_schedule_listing_data_source');

// Toggle auto-refresh on/off
provider.toggleAutoRefresh();

// Stop auto-refresh
provider.stopAutoRefresh();

// Start auto-refresh
provider.startAutoRefresh();

// Change refresh interval to 10 seconds
provider.setRefreshInterval(10000);
```

## Use Cases

### 1. **Monitor Order Export Jobs**
- Filter by `job_code`: `softcommerce_plenty_order_export`
- Watch real-time status as orders are exported
- Check messages for any errors

### 2. **Track Stock Synchronization**
- Filter by `job_code`: `softcommerce_plenty_stock_import`
- Monitor progress during large stock updates
- Verify completion times

### 3. **Debug Failed Jobs**
- Filter by `status`: Error
- Click on Messages column to view detailed error logs
- Use date range to find specific failures

### 4. **Performance Analysis**
- Compare `executed_at` and `finished_at` times
- Identify slow-running jobs
- Optimize cron schedules based on execution patterns

## Technical Details

### Files Created:
1. **Controller/Adminhtml/CronSchedule/Index.php** - Main controller
2. **view/adminhtml/layout/softcommerce_cronschedule_index.xml** - Layout definition
3. **view/adminhtml/web/js/grid/provider-auto-refresh.js** - Auto-refresh provider
4. **etc/adminhtml/menu.xml** - Menu entry (updated)
5. **view/adminhtml/ui_component/softcommerce_cron_schedule_listing.xml** - Grid configuration (updated)

### ACL Resource:
```
SoftCommerce_Profile::manage
```

### Database Table:
```
cron_schedule
```

## Troubleshooting

### Auto-Refresh Not Working:
1. Clear browser cache
2. Run: `php bin/magento cache:clean`
3. Run: `php bin/magento setup:upgrade`
4. Check browser console for JavaScript errors

### Grid Not Loading:
1. Verify ACL permissions for your admin user
2. Check module is enabled: `php bin/magento module:status`
3. Re-deploy static content: `php bin/magento setup:static-content:deploy`

### Performance Concerns:
- Increase refresh interval to 10-30 seconds
- Use filters to reduce data load
- Consider disabling auto-refresh when not actively monitoring

## Best Practices

1. **Use Filters**: Always filter by specific job_code when possible
2. **Limit Date Range**: Use date filters to reduce data volume
3. **Clean Up Old Records**: Regularly delete old completed/failed tasks
4. **Monitor During Operations**: Keep monitor open during critical sync operations
5. **Adjust Refresh Rate**: Slower intervals for monitoring, faster for debugging

## Future Enhancements

Possible additions:
- Export cron logs to CSV
- Email alerts for failed jobs
- Dashboard widgets
- Custom notifications
- Performance graphs