<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes" encoding="UTF-8"/>

    <xsl:param name="title" select="'Visura Soci e Cariche'"/>
    <xsl:param name="generated_at" select="''"/>
    <xsl:param name="logo_path" select="'../logoraces.jpg'"/>

    <xsl:template match="/">
        <html>
            <head>
                <meta charset="UTF-8"/>
                <title><xsl:value-of select="$title"/></title>
                <style type="text/css">
                    body {
                        font-family: Arial, sans-serif;
                        background: #f6f7fb;
                        margin: 0;
                        padding: 0 24px 24px 24px;
                        color: #1f2a37;
                    }
                    .page-header {
                        position: sticky;
                        top: 0;
                        display: flex;
                        align-items: center;
                        gap: 18px;
                        padding: 18px 0;
                        background: #f6f7fb;
                        z-index: 10;
                        border-bottom: 1px solid #e5e7eb;
                    }
                    .page-header img {
                        width: 140px;
                        height: auto;
                    }
                    h1 {
                        margin: 0;
                        font-size: 28px;
                        color: #111827;
                    }
                    .timestamp {
                        font-size: 13px;
                        color: #6b7280;
                        margin-top: 4px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                        background: #fff;
                        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
                        border-radius: 12px;
                        overflow: hidden;
                    }
                    thead {
                        background: #1d4ed8;
                        color: #fff;
                        text-transform: uppercase;
                        letter-spacing: 0.04em;
                    }
                    th, td {
                        padding: 12px 14px;
                        font-size: 13px;
                        vertical-align: top;
                        border-bottom: 1px solid #e5e7eb;
                    }
                    th {
                        font-weight: 600;
                        text-align: left;
                    }
                    tbody tr:nth-child(every) {
                        background: #f9fafb;
                    }
                    .pill {
                        display: inline-block;
                        padding: 4px 8px;
                        border-radius: 12px;
                        font-size: 12px;
                        font-weight: 600;
                        color: #1f2937;
                        background: #e0f2fe;
                    }
                    .flag-pill {
                        background: #fee2e2;
                        color: #991b1b;
                    }
                    .section-title {
                        font-size: 16px;
                        font-weight: 700;
                        margin: 24px 0 12px;
                        color: #0f172a;
                        text-transform: uppercase;
                        letter-spacing: 0.02em;
                    }
                    .nested {
                        margin-top: 8px;
                        padding: 8px 10px;
                        background: #f1f5f9;
                        border-radius: 8px;
                        font-size: 12px;
                        line-height: 1.4;
                    }
                    .nested strong {
                        color: #0f172a;
                    }
                    @media print {
                        @page { size: A4 portrait; margin: 18mm 15mm 20mm 15mm; }
                        .page-header { position: fixed; top: 0; left: 24px; right: 24px; background: #fff; }
                        body { margin-top: 160px; }
                        table { page-break-inside: auto; }
                        tr { page-break-inside: avoid; page-break-after: auto; }
                    }
                </style>
            </head>
            <body>
                <header class="page-header">
                    <img src="{$logo_path}" alt="Report logo"/>
                    <div>
                        <h1><xsl:value-of select="$title"/></h1>
                        <xsl:if test="string($generated_at) != ''">
                            <div class="timestamp">
                                Generato il: <xsl:value-of select="$generated_at"/>
                            </div>
                        </xsl:if>
                    </div>
                </header>

                <section>
                    <div class="section-title">Altre Cariche e Partecipazioni</div>
                    <xsl:choose>
                        <xsl:when test="count(//Othersubsidiaries) &gt; 0">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Soggetto</th>
                                        <th>Posizione</th>
                                        <th>Flags</th>
                                        <th>Dettagli collegati</th>
                                        <th>Indicatori CGR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="//Othersubsidiaries">
                                        <xsl:sort select="MainPositionPriority" data-type="number" order="ascending"/>
                                        <tr>
                                            <td>
                                                <xsl:value-of select="position()"/>
                                                <xsl:if test="normalize-space(MainPositionCode) != ''">
                                                    <div class="pill">
                                                        <xsl:value-of select="MainPositionCode"/>
                                                    </div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <strong><xsl:value-of select="OthersubsidiarieInfos"/></strong>
                                                <br/>
                                                <xsl:if test="normalize-space(TaxCode) != ''">
                                                    CF/P.IVA: <xsl:value-of select="TaxCode"/>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(ID) != ''">
                                                    <div>ID: <xsl:value-of select="ID"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(SubjectId) != ''">
                                                    <div>SubjectId: <xsl:value-of select="SubjectId"/></div>
                                                </xsl:if>
                                                <xsl:if test="IsPerson">
                                                    <div class="pill">
                                                        <xsl:text>Persona Fisica</xsl:text>
                                                    </div>
                                                </xsl:if>
                                                <xsl:if test="FlagPc = 'true'">
                                                    <div class="flag-pill">Partecipata</div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <div><strong><xsl:value-of select="MainPosition"/></strong></div>
                                                <xsl:if test="normalize-space(MainPositionPriority) != ''">
                                                    Priorità: <xsl:value-of select="MainPositionPriority"/>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(MainPositionSource) != ''">
                                                    <div>Fonte: <xsl:value-of select="MainPositionSource"/></div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:if test="FlagPP = 'true'">
                                                    <div class="pill">Flag PP</div>
                                                </xsl:if>
                                                <xsl:if test="FlagPc = 'true'">
                                                    <div class="pill">Flag PC</div>
                                                </xsl:if>
                                                <xsl:if test="FlagPc = 'false' and FlagPP = 'false'">
                                                    <div>-</div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="ConnectedPosition">
                                                        <div class="nested">
                                                            <strong>Ruolo collegato:</strong>
                                                            <div><xsl:value-of select="ConnectedPosition/MainPosition"/></div>
                                                            <xsl:if test="ConnectedPosition/MainPositionPriority">
                                                                Priorità: <xsl:value-of select="ConnectedPosition/MainPositionPriority"/>
                                                            </xsl:if>
                                                            <xsl:if test="ConnectedPosition/MainPositionSource">
                                                                <div>Fonte: <xsl:value-of select="ConnectedPosition/MainPositionSource"/></div>
                                                            </xsl:if>
                                                            <xsl:if test="ConnectedPosition/Subject">
                                                                <div>
                                                                    <strong>Referente:</strong>
                                                                    <xsl:value-of select="ConnectedPosition/Subject/Name"/>
                                                                </div>
                                                                <xsl:if test="ConnectedPosition/Subject/TaxCode">
                                                                    CF: <xsl:value-of select="ConnectedPosition/Subject/TaxCode"/>
                                                                </xsl:if>
                                                            </xsl:if>
                                                        </div>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <div>-</div>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="CgrFlags">
                                                        <div class="nested">
                                                            <xsl:for-each select="CgrFlags/*">
                                                                <div>
                                                                    <strong><xsl:value-of select="name()"/>:</strong>
                                                                    <xsl:value-of select="."/>
                                                                </div>
                                                            </xsl:for-each>
                                                        </div>
                                                    </xsl:when>
                                                    <xsl:otherwise>-</xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="nested">
                                Nessun elemento disponibile all'interno di &lt;Othersubsidiaries&gt;.
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </section>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
