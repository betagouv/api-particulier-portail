package apiparticulier

import scala.concurrent.duration._

import io.gatling.core.Predef._
import io.gatling.http.Predef._
import io.gatling.jdbc.Predef._

class HeavyLoadSimulation extends Simulation {

	val httpProtocol = http
		.baseUrl("http://localhost")
		.acceptHeader("*/*")

	val headers_0 = Map(
		"X-Api-Key" -> "yolo")

	val scn = scenario("HeavyLoadSimulation")
		.exec(http("request_0")
			.get("/api/sinkhole/yolo")
			.headers(headers_0))

	setUp(scn.inject(constantUsersPerSec(100) during(10) randomized)).protocols(httpProtocol)
}
