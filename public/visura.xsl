<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes" encoding="UTF-8"/>

    <xsl:param name="title" select="'Visura Completa'"/>
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
                    :root {
                        --header-height: 120px;
                    }
                    h1 {
                        margin-top: 0;
                        font-size: 28px;
                        color: #111827;
                    }
                    .timestamp {
                        font-size: 13px;
                        color: #6b7280;
                        margin-top: 4px;
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
                    }
                    .page-header img {
                        width: 140px;
                        height: auto;
                    }
                    .header-content {
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                    }
                    .content {
                        margin-top: 12px;
                    }
                    .section {
                        margin-top: 20px;
                    }
                    .section-title {
                        font-size: 16px;
                        font-weight: 700;
                        margin-bottom: 12px;
                        color: #0f172a;
                        text-transform: uppercase;
                        letter-spacing: 0.02em;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 18px;
                        background: #fff;
                        border-radius: 10px;
                        overflow: hidden;
                        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
                    }
                    thead {
                        background: #1d4ed8;
                        color: #fff;
                        text-transform: uppercase;
                        letter-spacing: 0.04em;
                    }
                    th, td {
                        padding: 10px 12px;
                        font-size: 13px;
                        border-bottom: 1px solid #e5e7eb;
                        text-align: left;
                    }
                    tbody tr:nth-child(even) {
                        background: #f9fafb;
                    }
                    tbody tr:last-child td {
                        border-bottom: none;
                    }
                    .element {
                        background: #ffffff;
                        border-radius: 8px;
                        margin-bottom: 12px;
                        padding: 16px 18px;
                        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
                        border-left: 4px solid #2563eb;
                    }
                    .element-name {
                        font-weight: bold;
                        font-size: 15px;
                        color: #1d4ed8;
                        text-transform: uppercase;
                    }
                    .value {
                        margin-top: 6px;
                        font-size: 14px;
                        color: #111827;
                        white-space: pre-wrap;
                        word-break: break-word;
                    }
                    .attributes {
                        margin-top: 10px;
                        padding-left: 18px;
                        font-size: 13px;
                        color: #0f172a;
                    }
                    .attributes li {
                        list-style-type: square;
                        margin-bottom: 4px;
                    }
                    .children {
                        margin-top: 12px;
                        border-left: 2px solid #e5e7eb;
                        padding-left: 18px;
                    }
                    .text-node {
                        font-size: 13px;
                        color: #374151;
                        background: #f3f4f6;
                        border-radius: 4px;
                        padding: 6px 8px;
                        display: inline-block;
                        margin-top: 6px;
                    }
                    @media print {
                        @page {
                            size: A4 portrait;
                            margin: 18mm 15mm 20mm 15mm;
                        }
                        body {
                            margin: calc(var(--header-height) + 10px) 0 20mm 0;
                            padding: 0 18px 18px 18px;
                        }
                        .page-header {
                            position: fixed;
                            top: 0;
                            left: 0;
                            right: 0;
                            padding: 12px 18px;
                            border-bottom: 1px solid #d1d5db;
                            background: #fff;
                        }
                        .content {
                            margin-top: 0;
                        }
                        table {
                            page-break-inside: auto;
                        }
                        tr {
                            page-break-inside: avoid;
                            page-break-after: auto;
                        }
                        .element {
                            break-inside: avoid-page;
                            page-break-inside: avoid;
                        }
                    }
                </style>
            </head>
            <body>
                <header class="page-header">
                    <img src="{$logo_path}" alt="Report logo"/>
                    <div class="header-content">
                        <h1><xsl:value-of select="//CompanyName"/></h1>
                        <xsl:if test="string($generated_at) != ''">
                            <div class="timestamp">
                                Generato il: <xsl:value-of select="$generated_at"/>
                            </div>
                        </xsl:if>
                    </div>
                </header>
                   <!-- DATI ANAGRAFICI SOCIETA -->
          <div class="section">
            <h1>Dati Società</h1>
            <table>
              <tr><th>Nome</th><td><xsl:value-of select="//CompanyName"/></td></tr>
              <tr><th>CF</th><td><xsl:value-of select="//TaxCode"/></td></tr>
              <tr><th>P.IVA</th><td><xsl:value-of select="//VATRegistrationNo"/></td></tr>
              <tr><th>Forma Legale</th><td><xsl:value-of select="//CompanyForm/LegalFormDescription"/></td></tr>
              <tr><th>Stato Attività</th><td><xsl:value-of select="//ActivityStatusDescription"/></td></tr>
              <tr><th>PEC</th><td><xsl:value-of select="//CertifiedEmail"/></td></tr>
              <tr><th>REA</th><td><xsl:value-of select="//REANo"/></td></tr>
              <tr><th>Data cost.</th><td>
                <xsl:value-of select="//IncorporationDate/year"/>-
                <xsl:value-of select="//IncorporationDate/month"/>-
                <xsl:value-of select="//IncorporationDate/day"/>
              </td></tr>
              <tr><th>Cod. Ateco</th><td><xsl:value-of select="//Ateco/Code"/>, <xsl:value-of select="//Ateco/Type"/></td></tr>
              <tr><th>Descrizione Attività</th><td><xsl:value-of select="//Activity/ActivityDescription"/></td></tr>
            </table>
          </div>
   <!-- INDIRIZZI, SEDI, UFFICIO LEGALE, FILIALI -->
  <h2>Indirizzi, Sedi e Unità Locali</h2>
<table>
  <tr>
    <th>Tipo</th>
    <th>Via</th>
    <th>Comune</th>
    <th>Provincia</th>
    <th>CAP</th>
  </tr>
  <xsl:for-each select="Indirizzi/SedeUnitaLocale">
    <tr>
      <td><xsl:value-of select="Tipo"/></td>
      <td><xsl:value-of select="Via"/></td>
      <td><xsl:value-of select="Comune"/></td>
      <td><xsl:value-of select="Provincia"/></td>
      <td><xsl:value-of select="CAP"/></td>
    </tr>
  </xsl:for-each>
</table>

                <main class="content">
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
                    <xsl:choose>
                        <xsl:when test="//SpecialSectionList/SpecialSection">
                            <section class="section">
                                <div class="section-title">Iscrizioni Sezioni Speciali</div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Codice</th>
                                            <th>Descrizione</th>
                                            <th>Prima iscrizione</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <xsl:for-each select="//SpecialSectionList/SpecialSection">
                                            <tr>
                                                <td><xsl:value-of select="position()"/></td>
                                                <td><xsl:value-of select="Code"/></td>
                                                <td>
                                                    <xsl:choose>
                                                        <xsl:when test="Code/@description">
                                                            <xsl:value-of select="Code/@description"/>
                                                        </xsl:when>
                                                        <xsl:otherwise>-</xsl:otherwise>
                                                    </xsl:choose>
                                                </td>
                                                <td>
                                                    <xsl:choose>
                                                        <xsl:when test="normalize-space(FirstInscriptionInSection) != ''">
                                                            <xsl:value-of select="FirstInscriptionInSection"/>
                                                        </xsl:when>
                                                        <xsl:otherwise>-</xsl:otherwise>
                                                    </xsl:choose>
                                                </td>
                                            </tr>
                                        </xsl:for-each>
                                    </tbody>
                                </table>
                            </section>
                        </xsl:when>
                        <xsl:otherwise/>
                    </xsl:choose>

                    <xsl:if test="//OfficialDirectors/Director">
                        <section class="section">
                            <div class="section-title">Direttori Ufficiali</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Direttore</th>
                                        <th>Incarichi</th>
                                        <th>Indirizzi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="//OfficialDirectors/Director">
                                        <tr>
                                            <td>
                                                <xsl:value-of select="position()"/>
                                                <xsl:if test="LegalRepresentativeRI = 'true'">
                                                    <div class="pill flag-pill" style="margin-top:4px;">Legale Rapp.</div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <strong>
                                                    <xsl:value-of select="normalize-space(concat(Individual/FirstName, ' ', Individual/LastName))"/>
                                                </strong>
                                                <xsl:if test="normalize-space(Individual/TaxCode) != ''">
                                                    <div>CF/P.IVA: <xsl:value-of select="Individual/TaxCode"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(Individual/SubjectId) != ''">
                                                    <div>SubjectId: <xsl:value-of select="Individual/SubjectId"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(Individual/BirthDate) != '' or normalize-space(Individual/BirthPlace) != ''">
                                                    <div>
                                                        <xsl:text>Nascita: </xsl:text>
                                                        <xsl:if test="normalize-space(Individual/BirthDate) != ''">
                                                            <xsl:value-of select="Individual/BirthDate"/>
                                                        </xsl:if>
                                                        <xsl:if test="normalize-space(Individual/BirthPlace) != ''">
                                                            <xsl:text> - </xsl:text>
                                                            <xsl:value-of select="Individual/BirthPlace"/>
                                                        </xsl:if>
                                                    </div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="IndividualPosition">
                                                        <xsl:for-each select="IndividualPosition">
                                                            <div class="nested" style="margin-bottom:6px;">
                                                                <strong><xsl:value-of select="Type"/></strong>
                                                                <xsl:if test="@Code">
                                                                    <span> (Codice: <xsl:value-of select="@Code"/>)</span>
                                                                </xsl:if>
                                                                <xsl:if test="normalize-space(StartDate) != ''">
                                                                    <div>Dal: <xsl:value-of select="StartDate"/></div>
                                                                </xsl:if>
                                                                <xsl:if test="normalize-space(Duration) != ''">
                                                                    <div>Durata: <xsl:value-of select="Duration"/></div>
                                                                </xsl:if>
                                                            </div>
                                                        </xsl:for-each>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <div>-</div>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                            <td>
                                                <xsl:if test="Individual/ResidenceAddress">
                                                    <div class="nested" style="margin-bottom:6px;">
                                                        <strong>Residenza:</strong>
                                                        <div>
                                                            <xsl:value-of select="Individual/ResidenceAddress/Street"/>
                                                        </div>
                                                        <div>
                                                            <xsl:value-of select="Individual/ResidenceAddress/PostCode"/>
                                                            <xsl:text> </xsl:text>
                                                            <xsl:value-of select="Individual/ResidenceAddress/Municipality"/>
                                                            <xsl:text> (</xsl:text>
                                                            <xsl:value-of select="Individual/ResidenceAddress/Province/@Code"/>
                                                            <xsl:text>)</xsl:text>
                                                        </div>
                                                    </div>
                                                </xsl:if>
                                                <xsl:if test="Individual/OtherAddress">
                                                    <div class="nested">
                                                        <strong>Altri indirizzi:</strong>
                                                        <xsl:for-each select="Individual/OtherAddress">
                                                            <div>
                                                                <xsl:value-of select="Street"/>
                                                                <xsl:if test="normalize-space(Municipality) != ''">
                                                                    <xsl:text>, </xsl:text>
                                                                    <xsl:value-of select="Municipality"/>
                                                                </xsl:if>
                                                                <xsl:if test="normalize-space(PostCode) != ''">
                                                                    <xsl:text> - </xsl:text>
                                                                    <xsl:value-of select="PostCode"/>
                                                                </xsl:if>
                                                            </div>
                                                        </xsl:for-each>
                                                    </div>
                                                </xsl:if>
                                                <xsl:if test="not(Individual/ResidenceAddress) and not(Individual/OtherAddress)">
                                                    <div>-</div>
                                                </xsl:if>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </section>
                    </xsl:if>

                    <xsl:if test="//Voices/*">
                        <section class="section">
                            <div class="section-title">Voci di Bilancio</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Voce</th>
                                        <th>Valore</th>
                                        <th>Dettagli</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="//Voices/*">
                                        <tr>
                                            <td><xsl:value-of select="position()"/></td>
                                            <td>
                                                <strong><xsl:value-of select="name()"/></strong>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="Value">
                                                        <xsl:value-of select="Value"/>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <xsl:value-of select="normalize-space(.)"/>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="@* or *[not(self::Value)]">
                                                        <div class="nested">
                                                            <xsl:if test="@*">
                                                                <xsl:for-each select="@*">
                                                                    <div>
                                                                        <strong><xsl:value-of select="name()"/>:</strong>
                                                                        <xsl:value-of select="."/>
                                                                    </div>
                                                                </xsl:for-each>
                                                            </xsl:if>
                                                            <xsl:for-each select="*[not(self::Value)]">
                                                                <div>
                                                                    <strong><xsl:value-of select="name()"/>:</strong>
                                                                    <xsl:value-of select="normalize-space(.)"/>
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
                        </section>
                    </xsl:if>

                    <xsl:if test="//TableRaw/*">
                        <section class="section">
                            <div class="section-title">Tabella Dettaglio (TableRaw)</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Voce</th>
                                        <th>Descrizione</th>
                                        <th>Importo</th>
                                        <th>Extra</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="//TableRaw/*">
                                        <tr>
                                            <td><xsl:value-of select="position()"/></td>
                                            <td><strong><xsl:value-of select="name()"/></strong></td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="normalize-space(Description) != ''">
                                                        <xsl:value-of select="Description"/>
                                                    </xsl:when>
                                                    <xsl:otherwise>-</xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="normalize-space(Amount) != ''">
                                                        <xsl:value-of select="Amount"/>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <xsl:value-of select="Value"/>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="@* or *[not(self::Description or self::Amount or self::Value)]">
                                                        <div class="nested">
                                                            <xsl:if test="@*">
                                                                <xsl:for-each select="@*">
                                                                    <div>
                                                                        <strong><xsl:value-of select="name()"/>:</strong>
                                                                        <xsl:value-of select="."/>
                                                                    </div>
                                                                </xsl:for-each>
                                                            </xsl:if>
                                                            <xsl:for-each select="*[not(self::Description or self::Amount or self::Value)]">
                                                                <div>
                                                                    <strong><xsl:value-of select="name()"/>:</strong>
                                                                    <xsl:value-of select="normalize-space(.)"/>
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
                        </section>
                    </xsl:if>

                    <xsl:if test="//ProfitAndLoss">
                        <section class="section">
                            <div class="section-title">Conto Economico (Profit &amp; Loss)</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Contesto</th>
                                        <th>Valori Principali</th>
                                        <th>Dettagli</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="//ProfitAndLoss">
                                        <tr>
                                            <td><xsl:value-of select="position()"/></td>
                                            <td>
                                                <strong>
                                                    <xsl:value-of select="name(..)"/>
                                                </strong>
                                                <xsl:if test="../ReferenceYear">
                                                    <div>Anno: <xsl:value-of select="../ReferenceYear"/></div>
                                                </xsl:if>
                                                <xsl:if test="../ClosingDate">
                                                    <div>Chiusura: <xsl:value-of select="../ClosingDate"/></div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:if test="Value">
                                                    <div><strong>Valore:</strong> <xsl:value-of select="Value"/></div>
                                                </xsl:if>
                                                <xsl:if test="Change">
                                                    <div><strong>Variazione:</strong> <xsl:value-of select="Change"/></div>
                                                </xsl:if>
                                                <xsl:if test="not(Value or Change)">
                                                    <div>-</div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="*[not(self::Value or self::Change)]">
                                                        <div class="nested">
                                                            <xsl:for-each select="*[not(self::Value or self::Change)]">
                                                                <div>
                                                                    <strong><xsl:value-of select="name()"/>:</strong>
                                                                    <xsl:choose>
                                                                        <xsl:when test="Value">
                                                                            <xsl:value-of select="Value"/>
                                                                        </xsl:when>
                                                                        <xsl:otherwise>
                                                                            <xsl:value-of select="normalize-space(.)"/>
                                                                        </xsl:otherwise>
                                                                    </xsl:choose>
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
                        </section>
                    </xsl:if>

                    <xsl:if test="//ExtraordinaryEvents/ExtraordinaryEventsList/EventItem">
                        <section class="section">
                            <div class="section-title">Eventi Straordinari</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo</th>
                                        <th>Transazione</th>
                                        <th>Date rilevanti</th>
                                        <th>Transferee</th>
                                        <th>Transferor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="//ExtraordinaryEvents/ExtraordinaryEventsList/EventItem">
                                        <tr>
                                            <td><xsl:value-of select="position()"/></td>
                                            <td>
                                                <strong><xsl:value-of select="Type"/></strong>
                                                <xsl:if test="normalize-space(TransferType) != ''">
                                                    <div class="pill" style="margin-top:4px;">
                                                        <xsl:value-of select="TransferType"/>
                                                    </div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:if test="normalize-space(TransfereeDescription) != ''">
                                                    <div><strong>Transferee:</strong> <xsl:value-of select="TransfereeDescription"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(TransferorDescription) != ''">
                                                    <div><strong>Transferor:</strong> <xsl:value-of select="TransferorDescription"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(MainPosition) != ''">
                                                    <div><xsl:value-of select="MainPosition"/></div>
                                                </xsl:if>
                                                <xsl:if test="not(normalize-space(TransfereeDescription) != '' or normalize-space(TransferorDescription) != '' or normalize-space(MainPosition) != '')">
                                                    <div>-</div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:if test="normalize-space(DeedDate) != ''">
                                                    <div><strong>Atto:</strong> <xsl:value-of select="DeedDate"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(RegisterDate) != ''">
                                                    <div><strong>Registrazione:</strong> <xsl:value-of select="RegisterDate"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(FilingDate) != ''">
                                                    <div><strong>Deposito:</strong> <xsl:value-of select="FilingDate"/></div>
                                                </xsl:if>
                                                <xsl:if test="normalize-space(ProtocolDate) != ''">
                                                    <div><strong>Protocollo:</strong> <xsl:value-of select="ProtocolDate"/></div>
                                                </xsl:if>
                                                <xsl:if test="not(normalize-space(DeedDate) != '' or normalize-space(RegisterDate) != '' or normalize-space(FilingDate) != '' or normalize-space(ProtocolDate) != '')">
                                                    <div>-</div>
                                                </xsl:if>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="normalize-space(TransfereeName) != ''">
                                                        <div><strong><xsl:value-of select="TransfereeName"/></strong></div>
                                                        <xsl:if test="normalize-space(TransfereeTaxCode) != ''">
                                                            CF/P.IVA: <xsl:value-of select="TransfereeTaxCode"/>
                                                        </xsl:if>
                                                    </xsl:when>
                                                    <xsl:when test="TransfereeList/Transferee">
                                                        <xsl:for-each select="TransfereeList/Transferee">
                                                            <div class="nested">
                                                                <strong><xsl:value-of select="Name"/></strong>
                                                                <xsl:if test="normalize-space(TaxCode) != ''">
                                                                    <div>CF/P.IVA: <xsl:value-of select="TaxCode"/></div>
                                                                </xsl:if>
                                                            </div>
                                                        </xsl:for-each>
                                                    </xsl:when>
                                                    <xsl:otherwise>-</xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                            <td>
                                                <xsl:choose>
                                                    <xsl:when test="normalize-space(TransferorName) != ''">
                                                        <div><strong><xsl:value-of select="TransferorName"/></strong></div>
                                                        <xsl:if test="normalize-space(TransferorTaxCode) != ''">
                                                            CF/P.IVA: <xsl:value-of select="TransferorTaxCode"/>
                                                        </xsl:if>
                                                    </xsl:when>
                                                    <xsl:when test="TransferorList/Transferor">
                                                        <xsl:for-each select="TransferorList/Transferor">
                                                            <div class="nested">
                                                                <strong><xsl:value-of select="Name"/></strong>
                                                                <xsl:if test="normalize-space(TaxCode) != ''">
                                                                    <div>CF/P.IVA: <xsl:value-of select="TaxCode"/></div>
                                                                </xsl:if>
                                                            </div>
                                                        </xsl:for-each>
                                                    </xsl:when>
                                                    <xsl:otherwise>-</xsl:otherwise>
                                                </xsl:choose>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </section>
                    </xsl:if>

                    <div class="children">
                        <xsl:apply-templates select="node()"/>
                    </div>
                </main>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="Voices|Voices/*|TableRaw|TableRaw/*|Othersubsidiaries|SpecialSectionList|SpecialSectionList/*|ExtraordinaryEvents|ExtraordinaryEvents/*|OfficialDirectors|OfficialDirectors/*|ProfitAndLoss|ProfitAndLoss/*"/>

    <xsl:template match="*">
        <div class="element">
            <div class="element-name">
                <xsl:value-of select="name()"/>
            </div>

            <xsl:if test="@*">
                <ul class="attributes">
                    <xsl:for-each select="@*">
                        <li>
                            <strong><xsl:value-of select="name()"/>:</strong>
                            <xsl:value-of select="."/>
                        </li>
                    </xsl:for-each>
                </ul>
            </xsl:if>

            <xsl:if test="not(*) and normalize-space(.) != ''">
                <div class="value">
                    <xsl:value-of select="normalize-space(.)"/>
                </div>
            </xsl:if>

            <xsl:if test="*">
                <div class="children">
                    <xsl:apply-templates select="node()"/>
                </div>
            </xsl:if>
        </div>
    </xsl:template>

    <xsl:template match="text()[normalize-space(.) != '']">
        <div class="text-node">
            <xsl:value-of select="normalize-space(.)"/>
        </div>
    </xsl:template>

    <xsl:template match="@*|comment()|processing-instruction()"/>
</xsl:stylesheet>
