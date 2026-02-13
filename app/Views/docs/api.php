<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>API 문서<?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
    /*
     * Redoc CSS 격리: Tailwind preflight가 Redoc 내부 요소에 영향을 주지 않도록
     * 브라우저 기본 스타일을 복원합니다.
     */
    #redoc-container {
        background: #ffffff;
        color: #333333;
    }
    #redoc-container h1, #redoc-container h2, #redoc-container h3,
    #redoc-container h4, #redoc-container h5, #redoc-container h6 {
        font-size: revert;
        font-weight: revert;
    }
    #redoc-container p, #redoc-container ul, #redoc-container ol,
    #redoc-container li, #redoc-container table, #redoc-container pre,
    #redoc-container code, #redoc-container blockquote {
        margin: revert;
        padding: revert;
    }
    #redoc-container ul, #redoc-container ol {
        list-style: revert;
    }
    #redoc-container a {
        color: revert;
        text-decoration: revert;
    }
    #redoc-container img, #redoc-container svg {
        display: revert;
        vertical-align: revert;
    }
    #redoc-container table {
        border-collapse: revert;
    }
    #redoc-container hr {
        border-top-width: revert;
    }
    #redoc-container button, #redoc-container input,
    #redoc-container select, #redoc-container textarea {
        font-family: revert;
        font-size: revert;
        color: revert;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="redoc-container"></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.redoc.ly/redoc/latest/bundles/redoc.standalone.js"></script>
<script>
    Redoc.init(
        '<?= base_url('docs/openapi.yaml') ?>',
        {
            scrollYOffset: function() {
                return document.querySelector('.navbar').offsetHeight;
            },
            hideDownloadButton: false,
            disableSearch: false,
            theme: {
                colors: {
                    primary: {
                        main: '#DD4814'
                    }
                },
                typography: {
                    fontSize: '15px',
                    fontFamily: 'Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", sans-serif',
                    headings: {
                        fontFamily: 'Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", sans-serif',
                        fontWeight: '600'
                    },
                    code: {
                        fontSize: '14px',
                        fontFamily: '"Monaco", "Menlo", "Ubuntu Mono", "Consolas", "source-code-pro", monospace'
                    }
                },
                sidebar: {
                    backgroundColor: '#fafafa',
                    textColor: '#333',
                    activeTextColor: '#DD4814'
                },
                rightPanel: {
                    backgroundColor: '#263238',
                    textColor: '#ffffff'
                }
            },
            nativeScrollbars: false,
            expandResponses: '200,201',
            requiredPropsFirst: true,
            sortPropsAlphabetically: false,
            showExtensions: true,
            pathInMiddlePanel: false,
            hideHostname: false,
            expandSingleSchemaField: true,
            jsonSampleExpandLevel: 2,
            hideSingleRequestSampleTab: true,
            menuToggle: true
        },
        document.getElementById('redoc-container')
    );
</script>
<?= $this->endSection() ?>
