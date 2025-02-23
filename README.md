# WhatsApp Floating Chat

A WordPress plugin that adds a fully customizable floating WhatsApp chat icon with inquiry options, animations, and analytics.

## Features

- Customizable WhatsApp floating button
- Multiple button positions (bottom-right, bottom-left, etc.)
- Different icon styles
- Pre-defined inquiry options
- Click tracking and analytics
- Mobile-friendly design
- Font Awesome integration

## Installation

1. Upload the `whatsapp-chat` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WhatsApp Chat settings to configure your WhatsApp number and preferences

## Configuration

### Basic Settings
1. Navigate to WhatsApp Chat settings in your WordPress admin panel
2. Enter your WhatsApp number (with country code)
3. Choose button position and style
4. Add inquiry options (optional)
5. Enable/disable click tracking

### Inquiry Options
Add pre-defined messages that users can choose from when they click the WhatsApp icon. Each option will open WhatsApp with a pre-filled message.

## Analytics

Track how users interact with your WhatsApp chat button:
- Number of clicks
- Most popular inquiry options
- Click timestamps
- Basic user information

## Developer Notes

### Hooks and Filters
The plugin provides several hooks for customization:
- `whatsapp_chat_number`
- `whatsapp_chat_position`
- `whatsapp_chat_options`

### CSS Classes
Main styling classes:
- `.whatsapp-chat-container`
- `.whatsapp-chat-button`
- `.whatsapp-popup`
- `.whatsapp-option`

## Support

For support, please visit https://crafely.com/whatsapp-floating-chat/

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Created by Mohamed Alamin (https://crafely.com)
