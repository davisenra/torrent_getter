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
        }
    }
}

