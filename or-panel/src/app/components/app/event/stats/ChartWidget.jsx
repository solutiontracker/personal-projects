import React, { Component } from 'react'
import { Translation, withTranslation } from "react-i18next";
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
import Signup from "@/app/event/stats/Signup";

am4core.useTheme(am4themes_animated);

class ChartWidget extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      popup: false,
      data: this.props.data,
      created_at: "",
    }
  }

  componentDidMount() {
    this._isMounted = true;
    this.createChart();
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevProps.data !== this.props.data) {
      this.createChart();
    }
  }

  static getDerivedStateFromProps(props, state) {
    if (props.data !== state.data) {
      return {
        data: props.data
      };
    }
    // Return null to indicate no change to state.
    return null;
  }

  createChart() {
    if (this._isMounted) {
      var response = this.state.data;
      let chart = am4core.create("chartdiv", am4charts.XYChart);
      chart.responsive.enabled = true;
      chart.scrollbarX = new am4core.Scrollbar();
      chart.scrollbarX.parent = chart.bottomAxesContainer;
      chart.scrollbarX.startGrip.disabled = true;
      chart.scrollbarX.endGrip.disabled = true;
      chart.scrollbarX.thumb.minWidth = 150;
      chart.zoomOutButton.disabled = true;
      let data = [];
      if (response.signups !== undefined) {
        Object.keys(response.signups).forEach(function (index) {
          if (response.signups[index].date !== undefined) {
            data.push({ "date": response.signups[index].date, "series1": (response.signups[index].count_1 !== undefined ? response.signups[index].count_1 : 0), "series2": (response.signups[index].count_2 !== undefined ? response.signups[index].count_2 : 0) });
          }
        })
      }
      chart.data = data;
      // Create axes
      var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
      categoryAxis.dataFields.category = "date";
      categoryAxis.renderer.grid.template.location = 0;
      categoryAxis.renderer.grid.template.disabled = true;
      categoryAxis.renderer.minGridDistance = 2;
      categoryAxis.renderer.labels.template.fill = am4core.color("#4F5154");
      categoryAxis.renderer.labels.template.wrap = true;
      categoryAxis.renderer.labels.template.textAlign = 'middle';
      categoryAxis.renderer.cellStartLocation = 0.1;
      categoryAxis.renderer.cellEndLocation = 0.9;

      var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
      valueAxis.renderer.inside = false;
      valueAxis.renderer.labels.template.disabled = false;
      valueAxis.renderer.labels.template.fill = am4core.color("#4F5154");
      valueAxis.min = 0;
      if (chart.data.length === 0) {
        var indicator;
        indicator = chart.tooltipContainer.createChild(am4core.Container);
        indicator.background.fill = am4core.color("#fff");
        indicator.background.fillOpacity = 0.8;
        indicator.width = am4core.percent(100);
        indicator.height = am4core.percent(100);

        var indicatorLabel = indicator.createChild(am4core.Label);
        indicatorLabel.text = this.props.t('DSB_NO_DATA_FOUND');
        indicatorLabel.align = "center";
        indicatorLabel.valign = "middle";
        indicatorLabel.fontSize = 20;
        valueAxis.max = 30;
      }
      else {
        // Legend
        chart.legend = new am4charts.Legend();
      }

      valueAxis.extraMax = 0.1;
      // valueAxis.strictMinMax = true;
      valueAxis.calculateTotals = true;

      // Create series
      function createSeries(field, name, total) {
        // Set up series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.name = name;
        series.dataFields.valueY = field;
        series.dataFields.categoryX = "date";
        series.sequencedInterpolation = true;

        // Make it stacked
        series.stacked = true;

        // Configure columns
        series.columns.template.width = am4core.percent(60);
        series.columns.template.tooltipText = "[bold]{name} : {valueY}";
        series.columns.template.events.on("hit", highlighColumn);

        // Add label
        // var labelBullet = series.bullets.push(new am4charts.LabelBullet());
        // labelBullet.label.text = "{valueY}";
        // labelBullet.label.fill = am4core.color("#fff");
        // labelBullet.locationY = 0.5;

        return series;
      }

      createSeries("series1", this.props.t('DSB_EVENT_ASSGINED_ATTENDEES'));
      createSeries("series2", this.props.t('DSB_ORDER_ATTENDEES'));

      // Add events
      categoryAxis.renderer.labels.template.events.on("hit", highlighColumn);
      categoryAxis.start = 0.8;
      categoryAxis.end = 1;
      categoryAxis.keepSelection = true;
      function highlighColumn(ev) {
        chart.series.each(function (series) {
          if (series instanceof am4charts.ColumnSeries) {
            series.columns.each(function (column) {
              if (column.dataItem.categoryX === ev.target.dataItem.category) {
                column.isActive = true;
              }
              else {
                column.isActive = false;
              }
            })
          }
        })
      }
    }
  }

  componentWillUnmount() {
    if (this.chart) {
      this.chart.dispose();
    }
    this._isMounted = false;
  }

  close = (type = false) => e => {
    this.setState({ popup: type });
  }

  render() {
    return (
      <Translation>
        {t => (
          <div className="wp-chart-wrapper">
            <header className="clearfix header-wrapper-widget">
              <div className="col-6">
                <h4>{t('DSB_SIGNUPS')}</h4>
                <p>{`${this.props.data.days_remaining}`}</p>
              </div>
            </header>
            <div className="body-charts-area">
              <h4 className="heading-top">{t('DSB_TOTAL_SEATS')}</h4>
              <div
                id="chartdiv"
                style={{ width: "100%", height: "258px" }}
              ></div>
              <h4 className="heading-bottom">{this.props.event.start_date}</h4>
            </div>
            {this.state.popup === "sign-ups" && <Signup close={this.close} created_at={this.state.created_at} />}
          </div>
        )}
      </Translation>
    );
  }
}

export default withTranslation()(ChartWidget)