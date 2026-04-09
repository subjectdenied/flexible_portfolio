import React, { Component } from 'react';

class TagPortfolio extends Component {
    static slug = 'et_pb_tag_portfolio';

    constructor(props) {
        super(props);
        this.state = { html: '', loading: true };
    }

    componentDidMount() {
        this.fetchPreview();
    }

    componentDidUpdate(prevProps) {
        const keys = [
            'post_type', 'filter_by', 'include_categories', 'include_tags',
            'include_posts', 'posts_number', 'show_filter', 'show_title',
            'show_categories', 'fullwidth', 'order',
        ];
        if (keys.some(k => prevProps[k] !== this.props[k])) {
            this.fetchPreview();
        }
    }

    fetchPreview() {
        this.setState({ loading: true });

        const data = new FormData();
        data.append('action', 'flex_portfolio_preview');
        data.append('nonce', window.FlexiblePortfolioBuilder.nonce);

        const keys = [
            'post_type', 'filter_by', 'include_categories', 'include_tags',
            'include_posts', 'posts_number', 'show_filter', 'show_title',
            'show_categories', 'fullwidth', 'columns', 'order',
        ];
        keys.forEach(k => {
            if (this.props[k]) {
                data.append(k, this.props[k]);
            }
        });

        const ajaxUrl = window.et_fb_options
            ? window.et_fb_options.ajaxurl
            : '/wp-admin/admin-ajax.php';

        fetch(ajaxUrl, { method: 'POST', body: data })
            .then(r => r.json())
            .then(d => {
                this.setState({
                    html: d.success ? d.data : '',
                    loading: false,
                });
            })
            .catch(() => {
                this.setState({ html: '', loading: false });
            });
    }

    render() {
        if (this.state.loading) {
            return (
                <div className="et_pb_tag_portfolio_loading" style={{
                    padding: '40px',
                    textAlign: 'center',
                    color: '#999',
                }}>
                    Laden...
                </div>
            );
        }

        if (!this.state.html) {
            return (
                <div className="et_pb_tag_portfolio_empty" style={{
                    padding: '40px',
                    textAlign: 'center',
                    color: '#999',
                }}>
                    Keine Beiträge gefunden.
                </div>
            );
        }

        return <div dangerouslySetInnerHTML={{ __html: this.state.html }} />;
    }
}

export default TagPortfolio;
