alert('ddd');

import 'ngx-extended-pdf-viewer';
import 'ngx-extended-pdf-viewer/assets/viewer.css';

document.addEventListener('DOMContentLoaded', () => {
    const pdfContainer = document.getElementById('pdf-container');
    if (pdfContainer) {
        pdfContainer.innerHTML = `
            <ngx-extended-pdf-viewer [src]="'/bord/test/documents/test.pdf'" useBrowserLocale="true" height="80vh"></ngx-extended-pdf-viewer>
        `;
    }
});
