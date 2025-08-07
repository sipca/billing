$("document").ready(function () {
    const chartByMinutes = document.getElementById('chart-pie-by-minutes');
    let dataTfByMinutes = JSON.parse($("#summaryTfByMinutes").html());
    console.log(dataTfByMinutes);

    new Chart(chartByMinutes, {
        type: 'pie',
        data: {
            labels: Object.keys(dataTfByMinutes),
            datasets: [{
                data: Object.values(dataTfByMinutes),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const rawSeconds = context.raw;
                            const formatted = formatDuration(rawSeconds);
                            return `${formatted}`;
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Calls duration'
                }
            }
        }
    });
});


function formatDuration(seconds) {
    const days = Math.floor(seconds / (24 * 3600));
    seconds %= (24 * 3600);
    const hours = Math.floor(seconds / 3600);
    seconds %= 3600;
    const minutes = Math.floor(seconds / 60);
    seconds %= 60;

    let result = '';
    if (days) result += `${days}d `;
    if (hours) result += `${hours}h `;
    if (minutes) result += `${minutes}m `;
    if (seconds || result === '') result += `${seconds}s`;
    return result.trim();
}