import React, { Component } from 'react';

class TagPortfolio extends Component {
    static slug = 'et_pb_tag_portfolio';

    constructor(props) {
        super(props);
        this.state = { activeFilter: 'all', currentPage: 1 };
    }

    handleFilterClick(e, slug) {
        e.preventDefault();
        e.stopPropagation();
        this.setState({ activeFilter: slug, currentPage: 1 });
    }

    handlePageClick(e, page) {
        e.preventDefault();
        e.stopPropagation();
        this.setState({ currentPage: page });
    }

    render() {
        const {
            __fp_items: items = [],
            __fp_terms: terms = {},
            show_filter = 'on',
            show_title = 'on',
            show_categories = 'on',
            show_pagination = 'on',
            posts_number = '12',
            fullwidth = 'off',
        } = this.props;

        const { activeFilter, currentPage } = this.state;

        if (!items || items.length === 0) {
            return (
                <div style={{ padding: '40px', textAlign: 'center', color: '#999' }}>
                    Bitte Kategorien, Schlagwörter oder Beiträge in den Moduleinstellungen auswählen.
                </div>
            );
        }

        const isGrid = fullwidth !== 'on';
        const termList = Object.values(terms);
        termList.sort((a, b) => a.label.localeCompare(b.label));

        // Filter items by active tab
        const filteredItems = activeFilter === 'all'
            ? items
            : items.filter(item =>
                item.category_classes &&
                item.category_classes.some(cls => cls === 'project_category_' + activeFilter)
            );

        // Paginate
        const perPage = parseInt(posts_number, 10) || 12;
        const totalPages = Math.ceil(filteredItems.length / perPage);
        const showPaging = show_pagination === 'on' && totalPages > 1;
        const startIdx = (currentPage - 1) * perPage;
        const visibleItems = showPaging
            ? filteredItems.slice(startIdx, startIdx + perPage)
            : filteredItems;

        return (
            <div className={`et_pb_module fp_tag_portfolio et_pb_filterable_portfolio clearfix ${isGrid ? 'et_pb_filterable_portfolio_grid' : 'et_pb_filterable_portfolio_fullwidth'}`}>
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
                <div className={`et_pb_portfolio_items_wrapper ${showPaging ? 'clearfix' : 'no_pagination'}`}>
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
                {showPaging && (
                    <div className="et_pb_portofolio_pagination clearfix">
                        <ul>
                            {currentPage > 1 && (
                                <li className="prev">
                                    <a href="#" onClick={(e) => this.handlePageClick(e, currentPage - 1)}>Vorherige</a>
                                </li>
                            )}
                            {Array.from({ length: totalPages }, (_, i) => i + 1).map(page => (
                                <li key={page} className={`page page-${page}`}>
                                    <a
                                        href="#"
                                        className={currentPage === page ? 'active' : ''}
                                        onClick={(e) => this.handlePageClick(e, page)}
                                    >
                                        {page}
                                    </a>
                                </li>
                            ))}
                            {currentPage < totalPages && (
                                <li className="next">
                                    <a href="#" onClick={(e) => this.handlePageClick(e, currentPage + 1)}>Nächste</a>
                                </li>
                            )}
                        </ul>
                    </div>
                )}
            </div>
        );
    }
}

export default TagPortfolio;
