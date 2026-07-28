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

    // Tooltips: click/tap toggles (hover/focus already shown via CSS alone)
    var tooltips = wrap.querySelectorAll('.csd-tooltip');
    tooltips.forEach(function (tip) {
        tip.addEventListener('click', function (e) {
            e.preventDefault();
            var wasActive = tip.classList.contains('is-active');
            tooltips.forEach(function (t) {
                t.classList.remove('is-active');
            });
            if (!wasActive) {
                tip.classList.add('is-active');
            }
        });
    });
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.csd-tooltip')) {
            tooltips.forEach(function (t) {
                t.classList.remove('is-active');
            });
        }
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

    // Generic clipboard copy, backed by a (possibly off-screen) textarea
    function copyTextareaToClipboard(sourceTextarea, successMessage) {
        var text = sourceTextarea.value;

        function fallbackCopy() {
            var originalPosition = sourceTextarea.style.position;
            var originalLeft = sourceTextarea.style.left;
            sourceTextarea.style.position = 'fixed';
            sourceTextarea.style.left = '0';
            sourceTextarea.focus();
            sourceTextarea.select();
            try {
                document.execCommand('copy');
                showToast(successMessage);
            } catch (err) {
                showToast('Unable to copy - please copy manually');
            }
            sourceTextarea.style.position = originalPosition;
            sourceTextarea.style.left = originalLeft;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                showToast(successMessage);
            }, fallbackCopy);
        } else {
            fallbackCopy();
        }
    }

    // Copy report
    var copyBtn = wrap.querySelector('.csd-copy-report');
    var textarea = wrap.querySelector('.csd-full-report-textarea');

    if (copyBtn && textarea) {
        copyBtn.addEventListener('click', function () {
            copyTextareaToClipboard(textarea, 'Report copied to clipboard');
        });
    }

    // Copy individual file content (.htaccess, robots.txt, debug.log)
    wrap.querySelectorAll('.csd-copy-file').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var section = btn.closest('.csd-section');
            var source = section ? section.querySelector('.csd-file-copy-source') : null;
            if (source) {
                copyTextareaToClipboard(source, 'Copied to clipboard');
            }
        });
    });

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

    // Tools tab: AJAX helper
    function csdAjaxPost(action, data) {
        var payload = Object.assign(
            { action: action, nonce: window.csdAdmin ? window.csdAdmin.nonce : '' },
            data || {}
        );
        var body = new URLSearchParams(payload);

        return fetch(window.csdAdmin ? window.csdAdmin.ajaxUrl : window.ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        }).then(function (response) {
            return response.json();
        });
    }

    function setResult(el, message, isError) {
        if (!el) {
            return;
        }
        el.textContent = message;
        el.classList.toggle('csd-tools-result-error', !!isError);
        el.classList.toggle('csd-tools-result-success', !isError);
    }

    function withDisabled(button, fn) {
        if (!button) {
            return;
        }
        button.disabled = true;
        fn().finally(function () {
            button.disabled = false;
        });
    }

    // Test email
    var testEmailBtn = wrap.querySelector('.csd-send-test-email');
    var testEmailResult = wrap.querySelector('.csd-test-email-result');
    if (testEmailBtn) {
        testEmailBtn.addEventListener('click', function () {
            withDisabled(testEmailBtn, function () {
                return csdAjaxPost('csd_send_test_email').then(function (res) {
                    setResult(testEmailResult, res.data && res.data.message, !res.success);
                });
            });
        });
    }

    // Save snapshot
    var saveSnapshotBtn = wrap.querySelector('.csd-save-snapshot');
    var compareSnapshotBtn = wrap.querySelector('.csd-compare-snapshot');
    var snapshotResult = wrap.querySelector('.csd-snapshot-result');
    var snapshotStatus = wrap.querySelector('.csd-snapshot-status');
    var snapshotDiff = wrap.querySelector('.csd-snapshot-diff');

    if (saveSnapshotBtn) {
        saveSnapshotBtn.addEventListener('click', function () {
            withDisabled(saveSnapshotBtn, function () {
                return csdAjaxPost('csd_save_snapshot').then(function (res) {
                    setResult(snapshotResult, res.data && res.data.message, !res.success);
                    if (res.success) {
                        if (snapshotStatus) {
                            snapshotStatus.innerHTML = 'Last snapshot saved: <strong>' + res.data.date + '</strong>';
                        }
                        if (compareSnapshotBtn) {
                            compareSnapshotBtn.disabled = false;
                        }
                    }
                });
            });
        });
    }

    if (compareSnapshotBtn) {
        compareSnapshotBtn.addEventListener('click', function () {
            withDisabled(compareSnapshotBtn, function () {
                return csdAjaxPost('csd_compare_snapshot').then(function (res) {
                    setResult(snapshotResult, res.success ? '' : (res.data && res.data.message), !res.success);
                    if (res.success && snapshotDiff) {
                        snapshotDiff.innerHTML = res.data.html;
                    }
                });
            });
        });
    }

    // Send report now
    var sendReportBtn = wrap.querySelector('.csd-send-report-now');
    var sendReportResult = wrap.querySelector('.csd-send-report-result');
    var reportEmailInput = wrap.querySelector('#csd-report-email');

    if (sendReportBtn) {
        sendReportBtn.addEventListener('click', function () {
            withDisabled(sendReportBtn, function () {
                return csdAjaxPost('csd_send_report_now', {
                    email: reportEmailInput ? reportEmailInput.value : ''
                }).then(function (res) {
                    setResult(sendReportResult, res.data && res.data.message, !res.success);
                });
            });
        });
    }

    // Report privacy: redact sensitive data toggle (auto-saves on change)
    var redactCheckbox = wrap.querySelector('#csd-redact-sensitive');
    var redactResult = wrap.querySelector('.csd-redact-result');
    if (redactCheckbox) {
        redactCheckbox.addEventListener('change', function () {
            csdAjaxPost('csd_save_redact_setting', {
                enabled: redactCheckbox.checked ? '1' : ''
            }).then(function (res) {
                setResult(redactResult, res.data && res.data.message, !res.success);
            });
        });
    }

    // Recurring report settings
    var saveRecurringBtn = wrap.querySelector('.csd-save-recurring-settings');
    var recurringResult = wrap.querySelector('.csd-recurring-result');
    var recurringEnabled = wrap.querySelector('#csd-recurring-enabled');
    var recurringRecipient = wrap.querySelector('#csd-recurring-recipient');
    var recurringFrequency = wrap.querySelector('#csd-recurring-frequency');

    if (saveRecurringBtn) {
        saveRecurringBtn.addEventListener('click', function () {
            withDisabled(saveRecurringBtn, function () {
                return csdAjaxPost('csd_save_recurring_settings', {
                    enabled: recurringEnabled && recurringEnabled.checked ? '1' : '',
                    recipient: recurringRecipient ? recurringRecipient.value : '',
                    frequency: recurringFrequency ? recurringFrequency.value : ''
                }).then(function (res) {
                    setResult(recurringResult, res.data && res.data.message, !res.success);
                });
            });
        });
    }
});
