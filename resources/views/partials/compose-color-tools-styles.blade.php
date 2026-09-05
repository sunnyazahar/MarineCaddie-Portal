.compose-toolbar .compose-color-picker {
    position: relative;
    display: inline-flex;
    align-items: center;
    align-self: flex-start;
    flex: 0 0 auto;
    vertical-align: middle;
}
.compose-toolbar .compose-color-tool-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    margin: 0 1px;
    padding: 0 4px;
    border: 0;
    border-radius: 3px;
    background: transparent;
    cursor: pointer;
}
.compose-toolbar .compose-color-tool-btn:hover,
.compose-toolbar .compose-color-picker.is-open .compose-color-tool-btn {
    background: #e5e7eb;
}
.compose-toolbar .compose-color-letter {
    font-size: 13px;
    font-weight: 700;
    line-height: 1;
    color: #111827;
    pointer-events: none;
    border-bottom: 3px solid #dc2626;
    padding: 0 1px 1px;
}
.compose-toolbar .compose-color-letter-highlight {
    border-bottom-color: #eab308;
    background: #fef9c3;
    padding: 1px 2px 1px;
    border-radius: 2px;
}
.compose-toolbar .compose-color-palette {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    z-index: 1200;
    width: 232px;
    padding: 8px;
    border: 1px solid #9ca3af;
    border-radius: 2px;
    background: #f3f4f6;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
    box-sizing: border-box;
}
.compose-toolbar .compose-color-palette[hidden] {
    display: none !important;
}
.compose-toolbar .compose-color-picker[data-color-mode="highlight"] .compose-color-palette {
    left: auto;
    right: 0;
}
.compose-toolbar .compose-color-auto,
.compose-toolbar .compose-color-more {
    display: flex;
    align-items: center;
    width: 100%;
    margin: 0;
    padding: 6px 8px;
    border: 1px solid #9ca3af;
    border-radius: 2px;
    background: #fff;
    color: #111827;
    font-size: 12px;
    line-height: 1.2;
    text-align: left;
    cursor: pointer;
}
.compose-toolbar .compose-color-auto:hover,
.compose-toolbar .compose-color-more:hover {
    background: #e5e7eb;
}
.compose-toolbar .compose-color-more {
    margin-top: 8px;
    gap: 6px;
    border-color: transparent;
    border-top: 1px solid #c4c4c4;
    border-radius: 0;
    background: transparent;
    padding-left: 4px;
}
.compose-toolbar .compose-color-more:hover {
    background: #e5e7eb;
}
.compose-toolbar .compose-color-section-label {
    margin: 8px 0 4px;
    color: #374151;
    font-size: 11px;
    font-weight: 600;
}
.compose-toolbar .compose-color-theme-bases,
.compose-toolbar .compose-color-theme-row,
.compose-toolbar .compose-color-standard {
    display: grid;
    grid-template-columns: repeat(10, 18px);
    gap: 2px;
    justify-content: space-between;
}
.compose-toolbar .compose-color-theme-shades {
    margin-top: 4px;
    display: grid;
    gap: 2px;
}
.compose-toolbar .compose-color-swatch {
    width: 18px;
    height: 18px;
    padding: 0;
    border: 1px solid rgba(0, 0, 0, 0.25);
    border-radius: 1px;
    cursor: pointer;
    box-sizing: border-box;
}
.compose-toolbar .compose-color-swatch:hover,
.compose-toolbar .compose-color-swatch.is-selected {
    outline: 1px solid #111827;
    outline-offset: 1px;
    border-color: #fff;
    box-shadow: 0 0 0 1px #111827;
    z-index: 1;
}
.compose-toolbar .compose-color-swatch[data-color="#ffffff"],
.compose-toolbar .compose-color-swatch[data-color="#ffff00"],
.compose-toolbar .compose-color-swatch[data-color="#f2f2f2"],
.compose-toolbar .compose-color-swatch[data-color="#e7e6e6"] {
    border-color: #b0b0b0;
}
.compose-toolbar .compose-color-native {
    position: absolute;
    left: 12px;
    bottom: 10px;
    width: 24px;
    height: 24px;
    opacity: 0;
    border: 0;
    padding: 0;
}
