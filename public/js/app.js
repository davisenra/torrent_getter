function app() {
    return {
        string: '',
        torrents: '',
        error: '',
        showLoading: false,
        showResults: false,
        resultsAmount: '',

        async fetchTorrents() {
            this.showLoading = true;
            this.showResults = false;

            let data = {
                "search-string": this.string
            };
            this.string = ''

            let response;

            try {
                let request = await fetch('/search', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })

                response = await request.json();

                this.resultsAmount = (response.data).length;
                this.torrents = response.data;
                this.showLoading = false;
                this.showResults = true;
            } catch (e) {
                console.error(e);
                this.error = 'Something went wrong...'
            }
        },

        formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0 Bytes'

            const k = 1024
            const dm = decimals < 0 ? 0 : decimals
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

            const i = Math.floor(Math.log(bytes) / Math.log(k))

            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
        },
    }
}

