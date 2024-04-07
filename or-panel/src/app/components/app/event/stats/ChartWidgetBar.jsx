import React, { Component } from 'react'
import { Translation, withTranslation } from "react-i18next";
import i18next from 'i18next';
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
am4core.useTheme(am4themes_animated);

class ChartWidgetBar extends Component {

  _isMounted = false;

  constructor(props) {
    super(props);

    i18next.on('languageChanged', () => {
      this.createChart();
    })
  }

  componentDidMount() {
    this._isMounted = true;
    this.createChart();
  }

  createChart() {
    if (this._isMounted) {
      var response = this.props.data;
      let chart = am4core.create("chartdiv2", am4charts.XYChart);
      if (Number(this.props.data.invited) > 0) {
        chart.data = [{
          "label": this.props.t('DSB_DELIVERABILITY_RATE'),
          "value": (response.attendee_invitation_stats !== undefined ? (Math.ceil(response.attendee_invitation_stats.sends) / Number(this.props.data.invited)) * 100 : 0),
          'color': '#C9170A'
        }, {
          "label": this.props.t('DSB_OPEN_RATE'),
          "value": (response.attendee_invitation_stats !== undefined ? (Math.ceil(response.attendee_invitation_stats.opens) / Number(this.props.data.invited)) * 100 : 0),
          'color': '#E68304'
        }, {
          "label": this.props.t('DSB_CLICK_RATE'),
          "value": (response.attendee_invitation_stats !== undefined ? (Math.ceil(response.attendee_invitation_stats.clicks) / Number(this.props.data.invited)) * 100 : 0),
          'color': '#F0D925'
        }, {
          "label": this.props.t('DSB_RESPONSE_RATE'),
          "value": (Math.ceil(response.registered_invited_attendees + response.not_attending) / Number(this.props.data.invited)) * 100,
          'color': '#6BC50F'
        }, {
          "label": this.props.t('DSB_CONVERSION_RATE'),
          "value": (Math.ceil(response.registered_invited_attendees) / Number(this.props.data.invited)) * 100,
          'color': '#5B9DD5'
        }, {
          "label": this.props.t('DSB_NOT_ATTENDING'),
          "value": (Math.ceil(response.not_attending) / Number(this.props.data.invited)) * 100,
          'color': '#583C5D'
        }];
      } else {
        chart.data = [];
      }
      let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
      categoryAxis.dataFields.category = "label";
      categoryAxis.renderer.grid.template.location = 0;
      categoryAxis.renderer.minGridDistance = 5;
      categoryAxis.renderer.grid.template.disabled = true;
      categoryAxis.renderer.labels.template.fill = am4core.color("#4F5154");
      categoryAxis.renderer.labels.template.wrap = true;
      categoryAxis.renderer.labels.template.maxWidth = 110;
      categoryAxis.renderer.labels.template.fontSize = 14;
      categoryAxis.renderer.labels.template.textAlign = 'middle';
      categoryAxis.renderer.cellStartLocation = 0.1;
      categoryAxis.renderer.cellEndLocation = 0.9;

      let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
      valueAxis.renderer.grid.template.disabled = false;
      valueAxis.renderer.labels.template.disabled = false;
      valueAxis.min = 0;
      valueAxis.max = 100;
      valueAxis.strictMinMax = false;
      if (chart.data.length === 0) {
        var indicator;
        indicator = chart.tooltipContainer.createChild(am4core.Container);
        indicator.background.fill = am4core.color("#fff");
        indicator.background.fillOpacity = 0.8;
        indicator.width = am4core.percent(100);
        indicator.height = am4core.percent(100);

        var indicatorLabel = indicator.createChild(am4core.Label);
        indicatorLabel.text = "No data Found.";
        indicatorLabel.align = "center";
        indicatorLabel.valign = "middle";
        indicatorLabel.fontSize = 20;
      }
      // Create series
      let series = chart.series.push(new am4charts.ColumnSeries());
      series.dataFields.valueY = "value";
      series.dataFields.categoryX = "label";
      series.dataFields.color = "color";
      series.name = "value";
      series.dataFields.color = "color";
      series.calculatePercent = false;
      series.columns.template.tooltipText = "{valueY}%";
      var labelBullet = series.bullets.push(new am4charts.LabelBullet());
      labelBullet.label.verticalCenter = "bottom";
      labelBullet.label.dy = 0;
      // labelBullet.label.text = "{valueY.percent}%";;
      series.stroke = 0;
      series.columns.template.adapter.add("fill", function (fill, target) {
        if (target.dataItem && target.dataItem.dataContext.color) {
          return am4core.color(target.dataItem.dataContext.color);
        } else {
          return fill;
        }
      });
      let fillModifier = new am4core.LinearGradientModifier();
      fillModifier.opacities = [0.7, 1, 1, 1];
      fillModifier.offsets = [0.5, 0.4, 1, 1, 1];
      series.columns.template.fillModifier = fillModifier;

      let columnTemplate = series.columns.template;
      columnTemplate.strokeWidth = 2;
      columnTemplate.strokeOpacity = 1;
    }
  }

  componentWillUnmount() {
    if (this.chart) {
      this.chart.dispose();
    }
    this._isMounted = false;
  }

  render() {
    return (
      <Translation>
        {t => (
          <div className="wp-chart-wrapper">
            <header style={{ margin: 0 }} className="clearfix header-wrapper-widget row ">
              <div className="col-6">
                <h4>{`${t('DSB_INVITE')} ${this.props.data.invited}`}</h4>
                <p>{`${t('DSB_SIGNUPS')} ${this.props.data.registered_invited_attendees}`}</p>
              </div>
              <div className="col-6 text-right">
                {this.props.data.attendee_invitation_stats && (
                  <div className="app-tooltip" style={{ position: "relative", top: "unset", right: "unset" }} >
                    <p>{t('DSB_HARD_BOUNCE_EMAILS')} <strong>{(this.props.data.attendee_invitation_stats.hard_bounce ? this.props.data.attendee_invitation_stats.hard_bounce : 0)}</strong></p>
                    <div className="app-tooltipwrapper">
                      {t('EMAIL_HARD_BOUNCE_TOOLTIP')}
                    </div>
                  </div>
                )}
                {this.props.data.attendee_invitation_stats && (
                  <div className="app-tooltip" style={{ position: "relative", top: "unset", right: "unset" }}>
                    <p>{t('DSB_SOFT_BOUNCE_EMAILS')} <strong>{(this.props.data.attendee_invitation_stats.soft_bounce ? this.props.data.attendee_invitation_stats.soft_bounce : 0)}</strong></p>
                    <div className="app-tooltipwrapper">
                      {t('EMAIL_SOFT_BOUNCE_TOOLTIP')}
                    </div>
                  </div>
                )}
              </div>
            </header>
            <div className="body-charts-area">
              <h4 className="heading-top">{t('DSB_EMAIL_NOTIFICATIONS')} <br />
                <span>{this.props.data.invited}</span>
              </h4>
              <div
                id="chartdiv2"
                style={{ width: "100%", height: "240px" }}
              ></div>
            </div>
          </div>
        )}
      </Translation>
    );
  }
}

export default withTranslation()(ChartWidgetBar);