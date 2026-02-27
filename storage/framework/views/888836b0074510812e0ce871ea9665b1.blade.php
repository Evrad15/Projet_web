
           <style>
    .fi-stats-overview-widget-stat div[class*="chart"] svg {
        animation: wave-pulse 2s infinite ease-in-out !important;
        transform-origin: bottom !important;
        display: block !important;
        overflow: visible !important;
    }

    
    .fi-stats-overview-widget-stat-description-icon, 
    .fi-stats-overview-widget-stat-description-icon svg {
        animation: none !important;
        transform: none !important;
    }

    @keyframes wave-pulse {
        0% { transform: scaleY(1); opacity: 0.8; }
        50% { transform: scaleY(1.8); opacity: 1; filter: brightness(1.2); }
        100% { transform: scaleY(1); opacity: 0.8; }
    }
</style>

        