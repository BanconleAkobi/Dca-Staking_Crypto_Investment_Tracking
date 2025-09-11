# FolioZen - Crypto Investment Tracking Project Context

## Project Overview
- **Project Name**: FolioZen (Crypto Investment Tracking)
- **Framework**: Symfony 7.x
- **Frontend**: Bootstrap 5, Webpack Encore
- **Database**: MySQL/PostgreSQL with Doctrine ORM
- **Features**: Asset management, transaction tracking, user authentication, billing

## Recent Issues & Solutions

### 1. Asset Management Route Conflict (RESOLVED ✅)
**Problem**: AssetController route `/assets` conflicted with Symfony's asset mapper
**Error**: `Asset with public path "/assets/" not found`
**Solution**: 
- Changed AssetController route from `/assets` to `/my-assets`
- Updated base.html.twig to use direct CSS links instead of Webpack Encore
- All existing template links continue to work (route names unchanged)

### 2. CSS Disappearing Issue (RESOLVED ✅)
**Problem**: CSS disappeared after attempting Webpack Encore integration
**Solution**: 
- Restored direct CSS links in base.html.twig: `<link rel="stylesheet" href="/styles/app.css">`
- Removed Webpack Encore functions temporarily
- CSS is now working properly

### 3. Node.js/npm Installation (COMPLETED ✅)
**Status**: Node.js v24.8.0 and npm v11.6.0 successfully installed
**Method**: Used Windows Package Manager (winget)
**Note**: Webpack Encore setup attempted but had configuration issues

### 4. Missing Privacy Policy Template (RESOLVED ✅)
**Problem**: LegalController referenced `legal/privacy.html.twig` but file didn't exist
**Solution**: 
- Created comprehensive privacy policy template
- Uses `dashboard/layout.html.twig` (consistent with other legal pages)
- Includes GDPR-compliant content with all required sections

## Current File Structure
```
templates/
├── base.html.twig (main layout)
├── dashboard/layout.html.twig (dashboard layout)
├── legal/
│   ├── privacy.html.twig ✅ (newly created)
│   ├── terms.html.twig
│   ├── disclaimer.html.twig
│   ├── refund.html.twig
│   └── contact.html.twig
└── [other templates...]

src/Controller/
├── AssetController.php (route: /my-assets)
├── LegalController.php
└── [other controllers...]
```

## Current Issues
- **UI/CSS Styling**: User mentioned interface is "not well centered" and "a bit ugly"
- **Need**: Improve visual design and layout centering

## Next Steps
1. Review and improve CSS styling for better centering
2. Enhance visual design elements
3. Consider Webpack Encore integration for better asset management (future)

## Technical Notes
- Asset system currently uses direct file serving from `/public/styles/`
- Route conflict resolved by changing AssetController path
- All legal pages now properly templated
- Node.js environment ready for future frontend improvements

---
*Last Updated: [Current Session]*
*Context maintained for continuity across conversations*
