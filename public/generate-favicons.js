// Favicon Generator for DCA Tracker
// This script generates PNG favicons from the SVG favicon

const canvas = document.createElement('canvas');
const ctx = canvas.getContext('2d');

// Function to generate favicon at specific size
function generateFavicon(size) {
    canvas.width = size;
    canvas.height = size;
    
    // Clear canvas
    ctx.clearRect(0, 0, size, size);
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, size, size);
    gradient.addColorStop(0, '#667eea');
    gradient.addColorStop(1, '#764ba2');
    
    // Draw background circle
    ctx.fillStyle = gradient;
    ctx.beginPath();
    ctx.arc(size/2, size/2, size/2 - 1, 0, 2 * Math.PI);
    ctx.fill();
    
    // Draw white border
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 1;
    ctx.stroke();
    
    // Draw chart line
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = Math.max(1, size/16);
    ctx.beginPath();
    
    const scale = size/32;
    ctx.moveTo(6 * scale, 22 * scale);
    ctx.lineTo(10 * scale, 18 * scale);
    ctx.lineTo(14 * scale, 20 * scale);
    ctx.lineTo(18 * scale, 14 * scale);
    ctx.lineTo(22 * scale, 16 * scale);
    ctx.lineTo(26 * scale, 12 * scale);
    ctx.stroke();
    
    // Draw data points
    ctx.fillStyle = '#fff';
    const pointRadius = Math.max(1, size/20);
    const points = [
        [10 * scale, 18 * scale],
        [14 * scale, 20 * scale],
        [18 * scale, 14 * scale],
        [22 * scale, 16 * scale]
    ];
    
    points.forEach(([x, y]) => {
        ctx.beginPath();
        ctx.arc(x, y, pointRadius, 0, 2 * Math.PI);
        ctx.fill();
    });
    
    // Draw crypto symbol (only for larger sizes)
    if (size >= 32) {
        ctx.fillStyle = '#fff';
        ctx.beginPath();
        ctx.arc(8 * scale, 8 * scale, 3 * scale, 0, 2 * Math.PI);
        ctx.fill();
        
        ctx.fillStyle = gradient;
        ctx.font = `bold ${4 * scale}px Arial`;
        ctx.textAlign = 'center';
        ctx.fillText('₿', 8 * scale, 10 * scale);
    }
    
    return canvas.toDataURL('image/png');
}

// Generate different sizes
const sizes = [16, 32, 180, 192, 512];
const favicons = {};

sizes.forEach(size => {
    favicons[size] = generateFavicon(size);
    console.log(`Generated ${size}x${size} favicon`);
});

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { generateFavicon, favicons };
}
