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
        <div class="section">
          <h2>Dettagli di Consegna e Riepilogo</h2>
          <xsl:if test="//DeliveryAndSummaryDetails">
            <table>
              <tr><th>Data consegna</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/DeliveredOn"/></td></tr>
              <tr><th>Situazione</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/CompanySituation"/></td></tr>
              <tr><th>In attività dal</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/ActiveOnTheMarketSince"/></td></tr>
              <tr><th>Ultimo aggiornamento stato attività</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/ActivityStatusStartDate"/></td></tr>
              <tr><th>Richieste Banche</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/CreditRequestsBanks"/></td></tr>
              <tr><th>Richieste ultime 12 mesi</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/CreditRequestsLastTwelveMonths"/></td></tr>
              <tr><th>Servizio richiesto</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/RequestedService"/></td></tr>
              <tr><th>Prodotto</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/ProductCode"/></td></tr>
              <tr><th>Codice gruppo</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/CervedGroupCode"/></td></tr>
              <tr><th>Cd Request</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/CdRequest"/></td></tr>
              <tr><th>Utente</th><td><xsl:value-of select="//DeliveryAndSummaryDetails/UserId"/></td></tr>
            </table>
          </xsl:if>
        </div>

        <div class="section">
          <h2>Sedi, Filiali e Unità Locali</h2>
          <h3>Sede legale</h3>
          <table>
            <tr>
              <th>Via</th>
              <th>Comune</th>
              <th>Provincia</th>
              <th>CAP</th>
              <th>Data inizio attività</th>
              <th>Ateco</th>
            </tr>
            <xsl:for-each select="//Offices/RegisteredOffice">
              <tr>
                <td><xsl:value-of select="RegisteredOfficeAddress/Street"/></td>
                <td><xsl:value-of select="RegisteredOfficeAddress/Municipality"/></td>
                <td><xsl:value-of select="RegisteredOfficeAddress/Province/@Code"/></td>
                <td><xsl:value-of select="RegisteredOfficeAddress/PostCode"/></td>
                <td><xsl:value-of select="ActivityStartDate"/></td>
                <td><xsl:value-of select="Ateco"/></td>
              </tr>
            </xsl:for-each>
          </table>

          <h3>Unità locali</h3>
<table>
  <tr>
              <th>ID Unità</th>
    <th>Tipo</th>
              <th>Brand</th>
    <th>Via</th>
    <th>Comune</th>
    <th>Provincia</th>
    <th>CAP</th>
              <th>Data apertura</th>
  </tr>
            <xsl:for-each select="//Offices/BusinessUnit">
              <tr>
                <td><xsl:value-of select="LocalUnit"/></td>
                <td><xsl:value-of select="Type"/></td>
                <td><xsl:value-of select="BrandName"/></td>
                <td><xsl:value-of select="OfficeAddress/Street"/></td>
                <td><xsl:value-of select="OfficeAddress/Municipality"/></td>
                <td><xsl:value-of select="OfficeAddress/Province/@Code"/></td>
                <td><xsl:value-of select="OfficeAddress/PostCode"/></td>
                <td><xsl:value-of select="BranchOpeningDate"/></td>
    </tr>
  </xsl:for-each>
</table>
        </div>
          <!-- AGGIORNAMENTI DATI UFFICIALI -->
          <div class="section">
            <h2>Aggiornamenti dati ufficiali</h2>
            <table class="official-updates">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Codice Fiscale</th>
                  <th>Codice Posizione Principale</th>
                  <th>Posizione Principale</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="//PositionCompany/PositionRelatedCompany">
                  <tr>
                    <td>
                      <xsl:value-of select="Director/Individual/FirstName"/>
                      <xsl:text> </xsl:text>
                      <xsl:value-of select="Director/Individual/LastName"/>
                    </td>
                    <td><xsl:value-of select="Director/Individual/TaxCode"/></td>
                    <td><xsl:value-of select="Director/IndividualPosition/MainPositionCode"/></td>
                    <td><xsl:value-of select="Director/IndividualPosition/Type"/></td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
            
            <h3>Ultimi aggiornamenti</h3>
            <table class="update-dates">
              <tr>
                <th>Ultima variazione anagrafica</th>
                <td>
                  <xsl:if test="//OfficialDataUpdate/LastRegistrationChangeDate">
                    <xsl:value-of select="format-number(//OfficialDataUpdate/LastRegistrationChangeDate/day, '00')"/>/<xsl:value-of select="format-number(//OfficialDataUpdate/LastRegistrationChangeDate/month, '00')"/>/<xsl:value-of select="//OfficialDataUpdate/LastRegistrationChangeDate/year"/>
                  </xsl:if>
                </td>
              </tr>
              <tr>
                <th>Ultimo controllo fallimenti</th>
                <td>
                  <xsl:if test="//OfficialDataUpdate/InsolvenciesCheckDate">
                    <xsl:value-of select="format-number(//OfficialDataUpdate/InsolvenciesCheckDate/day, '00')"/>/<xsl:value-of select="format-number(//OfficialDataUpdate/InsolvenciesCheckDate/month, '00')"/>/<xsl:value-of select="//OfficialDataUpdate/InsolvenciesCheckDate/year"/>
                  </xsl:if>
                </td>
              </tr>
              <tr>
                <th>Ultimo bilancio depositato</th>
                <td>
                  <xsl:if test="//OfficialDataUpdate/BalanceSheetClosingDate">
                    <xsl:value-of select="format-number(//OfficialDataUpdate/BalanceSheetClosingDate/day, '00')"/>/<xsl:value-of select="format-number(//OfficialDataUpdate/BalanceSheetClosingDate/month, '00')"/>/<xsl:value-of select="//OfficialDataUpdate/BalanceSheetClosingDate/year"/>
                  </xsl:if>
                </td>
              </tr>
            </table>
          </div>
          
          <!-- SPECIAL SECTIONS -->
          <div class="section">
            <h2>Iscrizioni e Sezioni Speciali</h2>
            <xsl:choose>
              <xsl:when test="//SpecialSectionList/SpecialSection">
                <table class="special-sections">
                  <thead>
                    <tr>
                      <th>Codice</th>
                      <th>Descrizione</th>
                      <th>Data Iscrizione</th>
                    </tr>
                  </thead>
                  <tbody>
                    <xsl:for-each select="//SpecialSectionList/SpecialSection">
                      <tr>
                        <td><xsl:value-of select="Code"/></td>
                        <td><xsl:value-of select="Description"/></td>
                        <td>
                          <xsl:if test="FirstInscriptionInSection">
                            <xsl:value-of select="format-number(FirstInscriptionInSection/day, '00')"/>/<xsl:value-of select="format-number(FirstInscriptionInSection/month, '00')"/>/<xsl:value-of select="FirstInscriptionInSection/year"/>
                          </xsl:if>
                        </td>
                      </tr>
                    </xsl:for-each>
                  </tbody>
                </table>
              </xsl:when>
              <xsl:otherwise>
                <p>Nessuna iscrizione o sezione speciale disponibile.</p>
              </xsl:otherwise>
            </xsl:choose>
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
            <table class="directors">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Cognome</th>
                  <th>Codice Fiscale</th>
                  <th>Ruolo</th>
                  <th>Data Inizio</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="//OfficialDirectors/Director">
                  <tr>
                    <td><xsl:value-of select="Individual/FirstName"/></td>
                    <td><xsl:value-of select="Individual/LastName"/></td>
                    <td><xsl:value-of select="Individual/TaxCode"/></td>
                    <td><xsl:value-of select="IndividualPosition/Type"/></td>
                    <td>
                      <xsl:value-of select="substring(IndividualPosition/StartDate, 9, 2)" />/
                      <xsl:value-of select="substring(IndividualPosition/StartDate, 6, 2)" />/
                      <xsl:value-of select="substring(IndividualPosition/StartDate, 1, 4)" />
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
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
            <table class="balance-sheet">
              <thead>
                <tr>
                  <th>Voce</th>
                  <xsl:for-each select="//BalanceSheetInfo">
                    <th><xsl:value-of select="ReferenceYear"/></th>
                  </xsl:for-each>
                </tr>
              </thead>
              <tbody>
                <!-- Profit and Loss -->
                <xsl:for-each select="//BalanceSheetInfo[1]/ProfitAndLoss/*">
                  <xsl:variable name="currentNode" select="name()"/>
                  <tr>
                    <td><xsl:value-of select="name()"/></td>
                    <xsl:for-each select="//BalanceSheetInfo">
                      <td><xsl:value-of select="ProfitAndLoss/*[name()=$currentNode]/Value"/></td>
                    </xsl:for-each>
                  </tr>
                </xsl:for-each>
                <!-- Assets -->
                <xsl:for-each select="//BalanceSheetInfo[1]/Assets/*">
                  <xsl:variable name="currentNode" select="name()"/>
                  <tr>
                    <td><xsl:value-of select="name()"/></td>
                    <xsl:for-each select="//BalanceSheetInfo">
                      <td><xsl:value-of select="Assets/*[name()=$currentNode]/Value"/></td>
                    </xsl:for-each>
                  </tr>
                </xsl:for-each>
                <!-- Liabilities and Shareholder Equity -->
                <xsl:for-each select="//BalanceSheetInfo[1]/LiabilitiesAndShareholderEquity/*">
                  <xsl:variable name="currentNode" select="name()"/>
                  <tr>
                    <td><xsl:value-of select="name()"/></td>
                    <xsl:for-each select="//BalanceSheetInfo">
                      <td><xsl:value-of select="LiabilitiesAndShareholderEquity/*[name()=$currentNode]/Value"/></td>
                    </xsl:for-each>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </div>
          <!-- INDICATORI DI SVILUPPO - RATING/SCOREING -->
          <div class="section">
            <h2>Indicatori e Scoring</h2>
            <table>
              <tr><th>Credito medio concedibile</th><td><xsl:value-of select="//AverageGrantableCredit/CreditLimit/Value"/></td></tr>
              <tr><th>Score MBD</th><td><xsl:value-of select="//AverageGrantableCredit/ScoreMBD"/></td></tr>
              <tr><th>Payline (assenza)</th><td><xsl:value-of select="//PaylineGradingAbsent/DescriptiveGradingSynthesisCode"/></td></tr>
              <tr><th>Cerved Group Score</th><td><xsl:value-of select="//CervedGroupScore/Score"/></td></tr>
              <tr><th>Rating type</th><td><xsl:value-of select="//CervedGroupScore/ratingType"/></td></tr>
              <tr><th>Governance grading</th><td><xsl:value-of select="//GovernanceGrading/Grading"/></td></tr>
              <tr><th>Governance sintesi</th><td><xsl:value-of select="//GovernanceGrading/DescriptiveGradingSynthesisCode"/></td></tr>
              <tr><th>Eventi negativi grading</th><td><xsl:value-of select="//NegativeEventsGrading/Grading"/></td></tr>
            </table>
          </div>

          <div class="section">
            <h2>Richieste ultimi 12 mesi</h2>
            <xsl:if test="//LastTwelveMonthRequests">
              <table>
                <tr><th>Totale richieste</th><td><xsl:value-of select="//LastTwelveMonthRequests/LastTwelveMonthTotalRequests"/></td></tr>
                <tr><th>Data ricerca</th><td><xsl:value-of select="//LastTwelveMonthRequests/SearchDate"/></td></tr>
                <tr><th>Periodo inizio</th><td><xsl:value-of select="//LastTwelveMonthRequests/BeginPeriod"/></td></tr>
                <tr><th>Periodo fine</th><td><xsl:value-of select="//LastTwelveMonthRequests/EndPeriod"/></td></tr>
                <tr><th>Aggiornato al</th><td><xsl:value-of select="//LastTwelveMonthRequests/UpdateDate"/></td></tr>
              </table>
              <table>
                <tr>
                  <th>Mese</th>
                  <th>Anno</th>
                  <th>Banche</th>
                  <th>Aziende</th>
                  <th>Altri</th>
                  <th>Totale</th>
                </tr>
                <xsl:for-each select="//LastTwelveMonthRequests/TableRaw">
                  <tr>
                    <td><xsl:value-of select="MonthYear/Month"/></td>
                    <td><xsl:value-of select="MonthYear/Year"/></td>
                     <td><xsl:if test="number(BankOrFinancialEntities) != 0"><xsl:value-of select="BankOrFinancialEntities"/></xsl:if><xsl:if test="number(BankOrFinancialEntities) = 0">&nbsp;</xsl:if></td>
                     <td><xsl:if test="number(Companies) != 0"><xsl:value-of select="Companies"/></xsl:if><xsl:if test="number(BankOrFinancialEntities) = 0">&nbsp;</xsl:if></td>
                     <td><xsl:if test="number(Others) != 0"><xsl:value-of select="Others"/></xsl:if><xsl:if test="number(Others) = 0">&nbsp;</xsl:if></td>
                     <td><xsl:if test="number(Total) != 0"><xsl:value-of select="Total"/></xsl:if><xsl:if test="number(Total) = 0">&nbsp;</xsl:if></td>
                  </tr>
                </xsl:for-each>
              </table>
            </xsl:if>
          </div>

          <div class="section">
            <h2>Posizione aziendale e società collegate</h2>
            <xsl:for-each select="//PositionCompany/PositionRelatedCompany">
              <div class="section">
                <h3>Direttore: <xsl:value-of select="Director/Individual/FirstName"/> <xsl:value-of select="Director/Individual/LastName"/></h3>
                <table>
                  <tr><th>Codice fiscale</th><td><xsl:value-of select="Director/Individual/TaxCode"/></td></tr>
                  <tr><th>Data nascita</th><td><xsl:value-of select="Director/Individual/BirthDate"/></td></tr>
                  <tr><th>Luogo</th><td><xsl:value-of select="Director/Individual/BirthPlace"/></td></tr>
                  <tr>
                    <th>Posizioni</th>
                    <td>
                      <xsl:for-each select="Director/Position">
                        <div>
                          <strong><xsl:value-of select="Type"/></strong>
                          (<xsl:value-of select="StartDate"/>)
                        </div>
                      </xsl:for-each>
                    </td>
                  </tr>
                </table>

                <h4>Società collegate</h4>
                <xsl:for-each select="RelatedCompany">
                  <table>
                    <tr><th>Società</th><td><xsl:value-of select="Aziendaslim/CompanyInformation/CompanyName"/></td></tr>
                    <tr><th>Codice fiscale</th><td><xsl:value-of select="Aziendaslim/CompanyInformation/TaxCode"/></td></tr>
                    <tr><th>Indirizzo</th><td>
                      <xsl:value-of select="Aziendaslim/CompanyInformation/Address/Street"/>
                      <xsl:text> </xsl:text>
                      <xsl:value-of select="Aziendaslim/CompanyInformation/Address/Municipality"/>
                      (<xsl:value-of select="Aziendaslim/CompanyInformation/Address/Province"/>)
                    </td></tr>
                    <tr><th>Stato attività</th><td><xsl:value-of select="Aziendaslim/ActivityDescription"/></td></tr>
                    <tr><th>Ateco</th><td><xsl:value-of select="Aziendaslim/Ateco"/></td></tr>
                    <tr><th>Ricavi</th><td><xsl:value-of select="Aziendaslim/Revenue/Value"/></td></tr>
                    <tr><th>Utile</th><td><xsl:value-of select="Aziendaslim/Profit/Value"/></td></tr>
                    <tr>
                      <th>Ruoli collegati</th>
                      <td>
                        <xsl:for-each select="Position">
                          <div>
                            <strong><xsl:value-of select="Type"/></strong>
                            (<xsl:value-of select="StartDate"/>)
                          </div>
                        </xsl:for-each>
                      </td>
                    </tr>
                  </table>
                </xsl:for-each>
              </div>
            </xsl:for-each>
          </div>

          <div class="section">
            <h2>Building Unit Real Time</h2>
            <xsl:if test="//BuildingUnitRealTime">
              <table>
                <tr><th>Denominazione</th><td><xsl:value-of select="//BuildingUnitRealTime/SurnameOrCompanyName"/></td></tr>
                <tr><th>Codice fiscale</th><td><xsl:value-of select="//BuildingUnitRealTime/TaxCode"/></td></tr>
                <tr><th>Esito immobili</th><td><xsl:value-of select="//BuildingUnitRealTime/SearchResultCompany/Result"/></td></tr>
                <tr><th>Esito esponenti</th><td><xsl:value-of select="//BuildingUnitRealTime/SearchResultDirectorOrShareholder/Result"/></td></tr>
              </table>
            </xsl:if>
          </div>

          <div class="section">
            <h2>Valutazione complessiva</h2>
            <xsl:if test="//OverallAssessment/Cebi4MedianSector">
              <h3>Mediane di settore</h3>
              <table>
                <tr><th>Codice</th><th>Valore</th></tr>
                <xsl:for-each select="//OverallAssessment/Cebi4MedianSector/MedianGeo">
                  <tr>
                    <td><xsl:value-of select="@Code"/></td>
                    <td><xsl:value-of select="."/></td>
                  </tr>
                </xsl:for-each>
              </table>
            </xsl:if>
            <xsl:if test="//OverallAssessment/Cebi4PD">
              <h3>Cebi4 PD</h3>
              <table>
                <tr>
                  <th>Data bilancio</th>
                  <th>PD</th>
                  <th>Score</th>
                  <th>Rating settore</th>
                </tr>
                <xsl:for-each select="//OverallAssessment/Cebi4PD">
                  <tr>
                    <td><xsl:value-of select="@dtCloseBalSheet"/></td>
                    <td><xsl:value-of select="PdAllGeoEiOverL"/></td>
                    <td><xsl:value-of select="CebiScore4GeoCode"/></td>
                    <td><xsl:value-of select="RatingMedianGeoSector"/></td>
                  </tr>
                </xsl:for-each>
              </table>
            </xsl:if>
          </div>

          <div class="section">
            <h2>Benchmarking CEBI4</h2>
            <xsl:if test="//Cebi4BenchSector">
              <table>
                <tr><th>Indicatore</th><th>Delta settore</th></tr>
                <xsl:for-each select="//Cebi4BenchSector/*">
                  <tr>
                    <td><xsl:value-of select="name()"/></td>
                    <td><xsl:value-of select="."/></td>
                  </tr>
                </xsl:for-each>
              </table>
            </xsl:if>
          </div>

          <div class="section">
            <h2>Holdings dai soci</h2>
            <xsl:if test="//HoldingsFromShareholders">
              <table>
                <tr><th>Numero partecipazioni</th><td><xsl:value-of select="//HoldingsFromShareholders/HoldingsNumber"/></td></tr>
              </table>
              <xsl:for-each select="//HoldingsFromShareholders/Holdings/Holding">
                <table>
                  <tr><th>Società</th><td><xsl:value-of select="AziendaSlim/CompanyInformation/CompanyName"/></td></tr>
                  <tr><th>Codice fiscale</th><td><xsl:value-of select="AziendaSlim/CompanyInformation/TaxCode"/></td></tr>
                  <tr><th>Provincia</th><td><xsl:value-of select="AziendaSlim/CompanyInformation/Address/Province"/></td></tr>
                  <tr><th>Attività</th><td><xsl:value-of select="AziendaSlim/ActivityDescription"/></td></tr>
                  <tr><th>Data atto</th><td><xsl:value-of select="DeedDate"/></td></tr>
                  <tr><th>Protocollo</th><td><xsl:value-of select="ProtocolNo"/></td></tr>
                  <tr>
                    <th>Quote</th>
                    <td>
                      <xsl:for-each select="Shares/Share">
                        <div>
                          % <xsl:value-of select="SharesPercentage"/>
                          - <xsl:value-of select="RightType/Right"/>
                        </div>
                      </xsl:for-each>
                    </td>
                  </tr>
                </table>
              </xsl:for-each>
            </xsl:if>
          </div>

          <div class="section">
            <h2>Titolari effettivi</h2>
            <xsl:if test="//BeneficialOwnersV2">
              <table>
                <tr><th>Numero titolari</th><td><xsl:value-of select="//BeneficialOwnersV2/BeneficialOwnerHead/NumberBeneficialOwner"/></td></tr>
                <tr><th>Versione</th><td><xsl:value-of select="//BeneficialOwnersV2/BeneficialOwnerHead/Version"/></td></tr>
              </table>
              <xsl:for-each select="//BeneficialOwnersV2/BeneficialOwner">
                <table>
                  <tr><th>Strategia</th><td><xsl:value-of select="@Strategy"/></td></tr>
                  <tr><th>Nome</th><td><xsl:value-of select="FirstName"/> <xsl:value-of select="LastName"/></td></tr>
                  <tr><th>Codice fiscale</th><td><xsl:value-of select="TaxCode"/></td></tr>
                  <tr>
                    <th>Data nascita</th>
                    <td>
                      <xsl:value-of select="BirthDate/@day"/>
                      <xsl:text>-</xsl:text>
                      <xsl:value-of select="BirthDate/@month"/>
                      <xsl:text>-</xsl:text>
                      <xsl:value-of select="BirthDate/@year"/>
                    </td>
                  </tr>
                  <tr><th>Comune</th><td><xsl:value-of select="Municipality"/></td></tr>
                  <tr><th>Indirizzo</th><td>
                    <xsl:value-of select="Toponym"/>
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="Address"/>
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="StreetNumber"/>
                  </td></tr>
                  <tr>
                    <th>Posizioni</th>
                    <td>
                      <xsl:for-each select="Positions/Position">
                        <div><xsl:value-of select="Description"/></div>
                      </xsl:for-each>
                    </td>
                  </tr>
                </table>
              </xsl:for-each>
            </xsl:if>
          </div>

          <div class="section">
            <h2>Aggiornamenti dati ufficiali</h2>
            <xsl:if test="//OfficialDataUpdate">
              <table>
                <tr><th>Ultima variazione registro</th><td><xsl:value-of select="//OfficialDataUpdate/LastRegistrationChangeDate"/></td></tr>
                <tr><th>Verifica insolvenze</th><td><xsl:value-of select="//OfficialDataUpdate/InsolvenciesCheckDate"/></td></tr>
                <tr><th>Data ultimo bilancio</th><td><xsl:value-of select="//OfficialDataUpdate/BalanceSheetClosingDate"/></td></tr>
                <tr><th>Ultimo aggiornamento holdings</th><td><xsl:value-of select="//OfficialDataUpdate/LastHoldingsFromShareholdersDate"/></td></tr>
            </table>
            </xsl:if>
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
