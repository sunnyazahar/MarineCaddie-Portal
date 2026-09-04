.compose-toolbar .compose-color-tool {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    margin: 0 1px;
    border-radius: 3px;
    cursor: pointer;
}
.compose-toolbar .compose-color-tool:hover {
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
.compose-toolbar .compose-color-tool input[type="color"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    border: 0;
    padding: 0;
}
