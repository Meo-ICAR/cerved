<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes"/>
    <xsl:template match="/">
      <html>
        <head>
          <title>Visura Completa</title>
          <style>
            body { font-family: Arial, sans-serif;}
            h1, h2, h3 { margin-top: 1.5em; }
            .section { margin: 2em 0; }
            table { border-collapse: collapse; width: 100%; margin-bottom:1em;}
            th, td { border: 1px solid #888; padding: 5px; }
            th { background: #eee; font-weight: bold; }
          </style>
        </head>
        <body>
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
          <!-- SPECIAL SECTIONS -->
          <div class="section">
            <h2>Iscrizioni e Sezioni Speciali</h2>
            <table>
              <tr>
                <th>Codice</th>
                <th>Descrizione</th>
                <th>Data Iscrizione</th>
              </tr>
              <xsl:for-each select="//SpecialSectionList/SpecialSection">
                  <tr>
                    <td><xsl:value-of select="Code"/></td>
                    <td><xsl:value-of select="description"/></td>
                    <td>
                      <xsl:value-of select="FirstInscriptionInSection/day"/>-
                      <xsl:value-of select="FirstInscriptionInSection/month"/>-
                      <xsl:value-of select="FirstInscriptionInSection/year"/>
                    </td>
                  </tr>
              </xsl:for-each>
            </table>
          </div>
          <!-- STAFF/DIPENDENTI -->
          <div class="section">
            <h2>Staff, Dipendenti e Personale</h2>
            <xsl:for-each select="//CompanyEmployee">
              <table>
                <tr><th>Anno</th><td><xsl:value-of select="Year"/></td></tr>
                <tr><th>Num. dipendenti</th><td><xsl:value-of select="NumberOfEmployees"/></td></tr>
              </table>
            </xsl:for-each>
            <h3>Suddivisione per Comune</h3>
            <xsl:for-each select="//MunicipalityEmployees">
              <table>
                <tr><th>Comune</th><td><xsl:value-of select="Municipality"/></td></tr>
                <tr><th>Provincia</th><td><xsl:value-of select="Province"/></td></tr>
                <tr><th>Num.</th><td><xsl:value-of select="LocalEmployeesNumber"/></td></tr>
                <tr><th>%</th><td><xsl:value-of select="PercentageLocalEmployees"/></td></tr>
              </table>
            </xsl:for-each>
          </div>
          <!-- ORGANI SOCIALI, DIRETTORI, REVISORI -->
          <div class="section">
            <h2>Organi Sociali e Direttori</h2>
            <table>
              <tr>
                <th>Nome</th><th>Cognome</th><th>CF</th>
                <th>Data Nascita</th><th>Comune Nascita</th>
                <th>Posizione</th><th>Data inizio</th><th>Durata</th>
                <th>Residenza</th>
              </tr>
              <xsl:for-each select="//OfficialDirectors/Director">
                <tr>
                  <td><xsl:value-of select="FirstName"/></td>
                  <td><xsl:value-of select="LastName"/></td>
                  <td><xsl:value-of select="TaxCode"/></td>
                  <td>
                    <xsl:value-of select="BirthDate/day"/>-
                    <xsl:value-of select="BirthDate/month"/>-
                    <xsl:value-of select="BirthDate/year"/>
                  </td>
                  <td><xsl:value-of select="BirthPlace"/></td>
                  <td><xsl:value-of select="IndividualPosition/Type"/></td>
                  <td>
                    <xsl:value-of select="IndividualPosition/StartDate/day"/>-
                    <xsl:value-of select="IndividualPosition/StartDate/month"/>-
                    <xsl:value-of select="IndividualPosition/StartDate/year"/>
                  </td>
                  <td><xsl:value-of select="IndividualPosition/Duration"/></td>
                  <td>
                    <xsl:value-of select="ResidenceAddress/Street"/>,
                    <xsl:value-of select="ResidenceAddress/Municipality"/>
                  </td>
                </tr>
              </xsl:for-each>
            </table>
          </div>
          <!-- PROTESTI ED EVENTI STRAORDINARI -->
          <div class="section">
            <h2>Protesti ed Eventi Straordinari</h2>
            <xsl:for-each select="//Protests">
              <p>Esistono protesti: <strong><xsl:value-of select="ProtestsSimilarityExists"/></strong></p>
            </xsl:for-each>
            <xsl:for-each select="//ExtraordinaryEventsList/EventItem">
              <table>
                <tr><th>Tipo evento</th><td><xsl:value-of select="Type"/></td></tr>
                <tr><th>Descrizione cedente/cessionario</th>
                  <td>
                    <xsl:value-of select="TransferorDescription"/>
                    <xsl:value-of select="TransfereeDescription"/>
                  </td>
                </tr>
                <tr><th>Data rogito</th>
                  <td>
                    <xsl:value-of select="DeedDate/day"/>-
                    <xsl:value-of select="DeedDate/month"/>-
                    <xsl:value-of select="DeedDate/year"/>
                  </td>
                </tr>
                <tr><th>Notary</th><td><xsl:value-of select="Notary"/></td></tr>
              </table>
            </xsl:for-each>
          </div>
          <!-- BILANCI ANALITICI MULTI-ANNO -->
          <div class="section">
            <h2>Bilanci Riclassificati Analitici</h2>
            <xsl:for-each select="//BalanceSheetInfo">
              <h3>Anno <xsl:value-of select="ReferenceYear"/></h3>
              <table>
                <xsl:for-each select="ProfitAndLoss/*">
                  <tr><th><xsl:value-of select="name()"/></th><td><xsl:value-of select="Value"/></td></tr>
                </xsl:for-each>
                <xsl:for-each select="Assets/*">
                  <tr><th><xsl:value-of select="name()"/></th><td><xsl:value-of select="Value"/></td></tr>
                </xsl:for-each>
                <xsl:for-each select="LiabilitiesAndShareholderEquity/*">
                  <tr><th><xsl:value-of select="name()"/></th><td><xsl:value-of select="Value"/></td></tr>
                </xsl:for-each>
              </table>
            </xsl:for-each>
          </div>
          <!-- INDICATORI DI SVILUPPO - RATING/SCOREING -->
          <div class="section">
            <h2>Indicatori Cerved, Rating, Scoring</h2>
            <table>
              <xsl:for-each select="//CompanyDevelopmentIndicators">
                <tr><th>Credito concedibile</th><td><xsl:value-of select="AverageGrantableCredit/Value"/></td></tr>
                <tr><th>Limite credito</th><td><xsl:value-of select="CreditLimit/Value"/></td></tr>
                <tr><th>Rating</th><td><xsl:value-of select="CervedGroupScore/Score"/></td></tr>
                <tr><th>Governance</th><td><xsl:value-of select="GovernanceGrading/DescriptiveGradingSynthesisCode"/></td></tr>
                <tr><th>Eventi negativi</th><td><xsl:value-of select="NegativeEventsGrading/Grading"/></td></tr>
              </xsl:for-each>
            </table>
          </div>
          <!-- SOCIETÀ COLLEGATE/CONTROLLATE/QUOTE -->
		  
          <div class="section">
<!-- Visualizzazione tabella Othersubsidiaries -->
<table>
  <tr>
    <th>Nome</th>
    <th>Codice Fiscale</th>
    <th>Codice Posizione Principale</th>
    <th>Posizione Principale</th>
    <th>Priorità</th>
    <th>Fonte Posizione</th>
    <th>ID</th>
    <th>Persona?</th>
    <th>SubjectId</th>
    <th>FlagPP</th>
    <th>FlagPc</th>
    <th>CgrPr</th>
    <th>CgrPa</th>
    <th>CgrPb</th>
    <th>CgrPc</th>
    <th>CgrF</th>
    <th>CgrPrA</th>
    <th>CgrPrC</th>
  </tr>
  <xsl:for-each select="Othersubsidiaries">
    <tr>
      <td><xsl:value-of select="OthersubsidiarieInfos"/></td>
      <td><xsl:value-of select="TaxCode"/></td>
      <td><xsl:value-of select="MainPositionCode"/></td>
      <td><xsl:value-of select="MainPosition"/></td>
      <td><xsl:value-of select="MainPositionPriority"/></td>
      <td><xsl:value-of select="MainPositionSource"/></td>
      <td><xsl:value-of select="ID"/></td>
      <td><xsl:value-of select="IsPerson"/></td>
      <td><xsl:value-of select="SubjectId"/></td>
      <td><xsl:value-of select="FlagPP"/></td>
      <td><xsl:value-of select="FlagPc"/></td>
      <td><xsl:value-of select="CgrFlags/CgrPr"/></td>
      <td><xsl:value-of select="CgrFlags/CgrPa"/></td>
      <td><xsl:value-of select="CgrFlags/CgrPb"/></td>
      <td><xsl:value-of select="CgrFlags/CgrPc"/></td>
      <td><xsl:value-of select="CgrFlags/CgrF"/></td>
      <td><xsl:value-of select="CgrFlags/CgrPrA"/></td>
      <td><xsl:value-of select="CgrFlags/CgrPrC"/></td>
    </tr>
  </xsl:for-each>
</table>

            <h2>Società Controllate / Collegate / Quote</h2>
            <xsl:for-each select="//Subsidiaries/OthersubsidiarieInfos">
              <table>
                <tr><th>Nome</th><td><xsl:value-of select="."/></td></tr>
                <tr><th>CF</th><td><xsl:value-of select="TaxCode"/></td></tr>
                <tr><th>Posizione</th><td><xsl:value-of select="MainPosition"/></td></tr>
              </table>
            </xsl:for-each>
            <h3>Partecipazioni / Proprietà</h3>
            <xsl:for-each select="//Shareholders">
              <table>
                <tr><th>Nome</th><td><xsl:value-of select="ShareholderName"/></td></tr>
                <tr><th>Percentuale</th><td><xsl:value-of select="SharesPercentage"/></td></tr>
              </table>
            </xsl:for-each>
          </div>
          <!-- NOTE e CAMPI LIBERI -->
          <div class="section">
            <h2>Note e Informazioni Libere</h2>
            <xsl:for-each select="//FreeText">
              <p><xsl:value-of select="."/></p>
            </xsl:for-each>
            <xsl:for-each select="//MyNotes">
              <p><xsl:value-of select="."/></p>
            </xsl:for-each>
          </div>
        </body>
      </html>
    </xsl:template>
</xsl:stylesheet>
