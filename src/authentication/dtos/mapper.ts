import { GraviteeTokenClaimsDTO } from 'src/authentication/dtos/gravitee-token-claims.dto';
import { User, UserEmail, UserId } from 'src/authentication/types/user.type';

export class GraviteeTokenClaimsMapper {
  static dtoToDomain(dto: GraviteeTokenClaimsDTO): User {
    return {
      id: dto.sub as UserId,
      firstname: dto.firstname,
      lastname: dto.lastname,
      email: dto.email as UserEmail,
    };
  }
}
