document.addEventListener('DOMContentLoaded', function () {
    var wrap = document.querySelector('.csd-wrap');
    if (!wrap) {
        return;
    }

    // Tab switching
    var tabs = wrap.querySelectorAll('.nav-tab');
    var panels = wrap.querySelectorAll('.csd-tab-panel');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            var target = tab.getAttribute('data-tab');

            tabs.forEach(function (t) {
                t.classList.remove('nav-tab-active');
            });
            panels.forEach(function (p) {
                p.classList.remove('is-active');
            });

            tab.classList.add('nav-tab-active');
            var panel = wrap.querySelector('.csd-tab-panel[data-tab="' + target + '"]');
            if (panel) {
                panel.classList.add('is-active');
            }
        });
    });

    // Toast helper
    var toast = wrap.querySelector('.csd-toast');
    var toastTimer = null;
    function showToast(message) {
        if (!toast) {
            return;
        }
        toast.textContent = message;
        toast.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            toast.classList.remove('is-visible');
        }, 2000);
    }

    // Copy report
    var copyBtn = wrap.querySelector('.csd-copy-report');
    var textarea = wrap.querySelector('.csd-report-textarea');

    if (copyBtn && textarea) {
        copyBtn.addEventListener('click', function () {
            var text = textarea.value;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    showToast('Report copied to clipboard');
                }, function () {
                    fallbackCopy();
                });
            } else {
                fallbackCopy();
            }

            function fallbackCopy() {
                textarea.style.position = 'fixed';
                textarea.style.left = '0';
                textarea.focus();
                textarea.select();
                try {
                    document.execCommand('copy');
                    showToast('Report copied to clipboard');
                } catch (err) {
                    showToast('Unable to copy - please copy manually');
                }
                textarea.style.position = '';
                textarea.style.left = '-9999px';
            }
        });
    }

    // Download report
    var downloadBtn = wrap.querySelector('.csd-download-report');
    if (downloadBtn && textarea) {
        downloadBtn.addEventListener('click', function () {
            var blob = new Blob([textarea.value], { type: 'text/plain' });
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = downloadBtn.getAttribute('data-filename') || 'system-details-report.txt';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        });
    }
});
