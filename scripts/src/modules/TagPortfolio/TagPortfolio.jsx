import React, { Component } from 'react';

class TagPortfolio extends Component {
    static slug = 'et_pb_tag_portfolio';

    constructor(props) {
        super(props);
        this.state = { activeFilter: 'all' };
    }

    handleFilterClick(e, slug) {
        e.preventDefault();
        this.setState({ activeFilter: slug });
    }

    render() {
        const {
            __fp_items: items = [],
            __fp_terms: terms = {},
            show_filter = 'on',
            show_title = 'on',
            show_categories = 'on',
            fullwidth = 'off',
        } = this.props;

        const { activeFilter } = this.state;

        if (!items || items.length === 0) {
            return (
                <div style={{ padding: '40px', textAlign: 'center', color: '#999' }}>
                    Bitte Kategorien, Schlagw{'\u00f6'}rter oder Beitr{'\u00e4'}ge in den Moduleinstellungen ausw{'\u00e4'}hlen.
                </div>
            );
        }

        const isGrid = fullwidth !== 'on';
        const termList = Object.values(terms);
        termList.sort((a, b) => a.label.localeCompare(b.label));

        // Filter items by active tab
        const visibleItems = activeFilter === 'all'
            ? items
            : items.filter(item =>
                item.category_classes &&
                item.category_classes.some(cls => cls === 'project_category_' + activeFilter)
            );

        return (
            <div className={`et_pb_module et_pb_filterable_portfolio clearfix ${isGrid ? 'et_pb_filterable_portfolio_grid' : 'et_pb_filterable_portfolio_fullwidth'}`}>
                {show_filter === 'on' && termList.length > 0 && (
                    <div className="et_pb_portfolio_filters clearfix">
                        <ul className="clearfix">
                            <li className="et_pb_portfolio_filter et_pb_portfolio_filter_all">
                                <a
                                    href="#"
                                    className={activeFilter === 'all' ? 'active' : ''}
                                    onClick={(e) => this.handleFilterClick(e, 'all')}
                                >
                                    Alle
                                </a>
                            </li>
                            {termList.map(term => (
                                <li key={term.slug} className="et_pb_portfolio_filter">
                                    <a
                                        href="#"
                                        className={activeFilter === term.slug ? 'active' : ''}
                                        onClick={(e) => this.handleFilterClick(e, term.slug)}
                                    >
                                        {term.label}
                                    </a>
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
                <div className="et_pb_portfolio_items_wrapper clearfix">
                    <div className="et_pb_portfolio_items">
                        {visibleItems.map(item => (
                            <div
                                key={item.id}
                                className={`et_pb_portfolio_item active ${isGrid ? 'et_pb_grid_item' : ''} ${(item.category_classes || []).join(' ')}`}
                                style={{ display: 'block' }}
                            >
                                {item.thumbnail && (
                                    <a href={item.permalink}>
                                        <span className="et_portfolio_image">
                                            <span dangerouslySetInnerHTML={{ __html: item.thumbnail }} />
                                            <span className="et_overlay"></span>
                                        </span>
                                    </a>
                                )}
                                {show_title === 'on' && (
                                    <h2 className="et_pb_module_header">
                                        <a href={item.permalink}>{item.title}</a>
                                    </h2>
                                )}
                                {show_categories === 'on' && item.post_categories && item.post_categories.length > 0 && (
                                    <p className="post-meta">
                                        {item.post_categories.map(c => c.label).join(', ')}
                                    </p>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        );
    }
}

export default TagPortfolio;
